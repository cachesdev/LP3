function WindowCenter(theURL, winName, features, myWidth, myHeight, isCenter) {
    if (window.screen) {
        if (isCenter) {
            if (isCenter == "true") {
                var myLeft = (screen.width - myWidth) / 2;
                var myTop = (screen.height - myHeight) / 2;
                features += features != "" ? "," : "";
                features += ",left=" + myLeft + ",top=" + myTop;
            }
        }
    }
    window.open(
        theURL,
        winName,
        features +
            (features != "" ? "," : "") +
            "width=" +
            myWidth +
            ",height=" +
            myHeight,
    );
}
