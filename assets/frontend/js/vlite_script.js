window.Vlitejs.registerProvider('youtube', window.VlitejsYoutube);
new window.Vlitejs("#player", {
    options: {
        controls: false,
        autoplay: true,
        muted: false,
        autoHide: true,
        bigPlay: false,
        fullscreen: true,
        loop: true,
        providerParams: {
          responsive: true,
        }
    },
    provider: ["youtube"],
    onReady: function (player) {
        console.log(player);
        player.on("play", () => console.log("play"));
        player.on("pause", () => console.log("pause"));
        player.on("progress", () => console.log("progress"));
        player.on("ended", () => console.log("ended"));
    },
});