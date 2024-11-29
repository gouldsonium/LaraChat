export const formatMessage = (message) => {
    // Escape code blocks first, so they are not affected by other formatting
    message = message.replace(
        /```([^`]+)```/g, // Match text wrapped in triple backticks
        (match) => `<codeblock>${match}</codeblock>`
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
        /\*\*([^\*]+)\*\*/g,  // Match text wrapped in **
        '<b>$1</b>'
    );
    message = message.replace(
        /__([^_]+)__/g,  // Match text wrapped in __
        '<b>$1</b>'
    );

    // Convert italic markdown *text* or _text_ to <i> tags
    message = message.replace(
        /\*([^\*]+)\*/g,  // Match text wrapped in *
        '<i>$1</i>'
    );
    message = message.replace(
        /_([^_]+)_/g,  // Match text wrapped in _
        '<i>$1</i>'
    );

    // Convert inline code markdown `text` to <code> tags
    message = message.replace(
        /`([^`]+)`/g,  // Match text wrapped in backticks
        '<code>$1</code>'
    );

    // Convert strikethrough markdown ~~text~~ to <del> tags
    message = message.replace(
        /~~([^~]+)~~/g,  // Match text wrapped in ~~
        '<del>$1</del>'
    );

    // Restore code blocks by replacing <codeblock> with the original code block markup
    message = message.replace(
        /<codeblock>(```[^`]+```)<\/codeblock>/g, // Restore the original code block markup
        (match, codeBlock) => codeBlock
    );

    return message;
};
