const copyButtonLabel = "Copy";

$( document ).ready(function() {
    // use a class selector if available
    let blocks = document.querySelectorAll("pre.copy-enabled");

    blocks.forEach((block) => {
        // only add button if browser supports Clipboard API
        if (navigator.clipboard) {
            let button = document.createElement("button");

            button.innerText = copyButtonLabel;
            button.setAttribute('class', 'copy-button btn btn-sm btn-outline-primary');
            button.setAttribute('title','Copy to clipboard');
            block.appendChild(button);

            button.addEventListener("click", async () => {
                await copyCode(block);
            });
        }
    });

});
async function copyCode(block) {
    let code = block.querySelector("code");
    let text = code.innerText;

    await navigator.clipboard.writeText(text);
}