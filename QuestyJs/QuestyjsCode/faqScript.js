const blobEl = document.getElementById("blob");

document.body.onpointermove = event => {
    const { clientX, clientY } = event;

    blobEl.animate({
        left: clientX + "px",
        top: clientY + "px"
    }, {duration: 3000, fill: "forwards"})
}