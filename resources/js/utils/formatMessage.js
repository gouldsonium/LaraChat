export const formatMessage = (message) => {
    // Convert code blocks with optional language specifier to <pre><code> with language class
    message = message.replace(
        /```(\w+)?\n([\s\S]*?)```/g, // Match ```language\ncode```
        (match, language, code) => {
            const langClass = language ? ` class="language-${language}"` : '';
            return `<pre><code${langClass}>${code.trim()}</code></pre>`;
        }
    );

    // Replace --- with <hr> tags
    message = message.replace(/^\s*---\s*$/gm, '<hr class="mt-5">');

    // Convert markdown tables to HTML tables
    message = message.replace(
        /((?:\|.*\|(?:\r?\n|\r))+)/g, // Match markdown table structure
        (table) => {
            const rows = table.trim().split('\n'); // Split the table into rows
            const header = rows[0]
                .trim()
                .split('|')
                .slice(1, -1) // Extract header columns
                .map((cell) => `<th>${cell.trim()}</th>`)
                .join('');
            const body = rows
                .slice(2) // Skip header and separator
                .map((row) =>
                    `<tr>${row
                        .trim()
                        .split('|')
                        .slice(1, -1) // Extract body columns
                        .map((cell) => `<td>${cell.trim()}</td>`)
                        .join('')}</tr>`
                )
                .join('');
            return `<table class="table-auto border-collapse border border-gray-300 w-full"><thead><tr>${header}</tr></thead><tbody>${body}</tbody></table>`;
        }
    );

    // Convert # tags to headings (h1 to h6) for individual lines
    message = message.replace(
        /^(#{1,6})\s+(.+)$/gm, // Match lines starting with 1-6 # characters
        (_, hashes, content) => `<h${hashes.length}>${content.trim()}</h${hashes.length}>`
    );

    // Convert newlines to <br> but prevent newlines after headings
    message = message.replace(/(?<!<\/h[1-6]>)(\n)/g, '<br/>'); // Avoid adding <br> after headings

    // Convert markdown links [text](url) to <a> tags
    message = message.replace(
        /\[([^\]]+)]\((https?:\/\/[^\s]+)\)/g,
        '<a href="$2" target="_blank" class="text-primary underline">$1</a>'
    );

    // Convert bold markdown **text** or __text__ to <b> tags
    message = message.replace(
        /\*\*([^\*]+)\*\*/g, // Match text wrapped in **
        '<b>$1</b>'
    );
    message = message.replace(
        /__([^_]+)__/g, // Match text wrapped in __
        '<b>$1</b>'
    );

    // Convert italic markdown *text* or _text_ to <i> tags
    message = message.replace(
        /\*([^\*]+)\*/g, // Match text wrapped in *
        '<i>$1</i>'
    );
    message = message.replace(
        /_([^_]+)_/g, // Match text wrapped in _
        '<i>$1</i>'
    );

    // Convert inline code markdown `text` to <code> tags
    message = message.replace(
        /`([^`]+)`/g, // Match text wrapped in backticks
        '<code>$1</code>'
    );

    // Convert strikethrough markdown ~~text~~ to <del> tags
    message = message.replace(
        /~~([^~]+)~~/g, // Match text wrapped in ~~
        '<del>$1</del>'
    );

    // Convert Markdown task lists
    message = message.replace(
        /^-\s+\[x\]\s+(.+)$/gm, // Match completed tasks
        '<li><input type="checkbox" checked disabled> $1</li>'
    );
    message = message.replace(
        /^-\s+\[\s\]\s+(.+)$/gm, // Match incomplete tasks
        '<li><input type="checkbox" disabled> $1</li>'
    );

    // Wrap task list items in a <ul> if any task lists are present
    if (message.match(/<li><input type="checkbox".*<\/li>/)) {
        message = message.replace(
            /(<li><input type="checkbox".*<\/li>)/g,
            `<ul class="list-none space-y-1">$1</ul>`
        );
    }

    return message;
};
