// utils/formatMessage.js
export const formatMessage = (message) => {
    // Convert newlines to <br>
    let formattedMessage = message.replace(/\n/g, '<br/>');

    // Convert markdown links [text](url) to <a> tags
    formattedMessage = formattedMessage.replace(
        /\[([^\]]+)]\((https?:\/\/[^\s]+)\)/g,
        '<a href="$2" target="_blank" class="text-primary underline">$1</a>'
    );

    // Convert bold markdown **text** or __text__ to <b> tags
    formattedMessage = formattedMessage.replace(
        /\*\*([^\*]+)\*\*/g,  // Match text wrapped in **
        '<b>$1</b>'
    );
    formattedMessage = formattedMessage.replace(
        /__([^_]+)__/g,  // Match text wrapped in __
        '<b>$1</b>'
    );

    // Convert italic markdown *text* or _text_ to <i> tags
    formattedMessage = formattedMessage.replace(
        /\*([^\*]+)\*/g,  // Match text wrapped in *
        '<i>$1</i>'
    );
    formattedMessage = formattedMessage.replace(
        /_([^_]+)_/g,  // Match text wrapped in _
        '<i>$1</i>'
    );

    // Convert inline code markdown `text` to <code> tags
    formattedMessage = formattedMessage.replace(
        /`([^`]+)`/g,  // Match text wrapped in backticks
        '<code>$1</code>'
    );

    // Convert code block markdown ```text``` to <pre><code> tags
    formattedMessage = formattedMessage.replace(
        /```([^`]+)```/g,  // Match text wrapped in triple backticks
        '<pre><code>$1</code></pre>'
    );

    // Convert strikethrough markdown ~~text~~ to <del> tags
    formattedMessage = formattedMessage.replace(
        /~~([^~]+)~~/g,  // Match text wrapped in ~~
        '<del>$1</del>'
    );

    return formattedMessage;
};
