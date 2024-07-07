jQuery(document).ready(function($) {
    function updatePreview() {
        var color1 = $('#lpc-color1').val();
        var color2 = $('#lpc-color2').val();
        var gradientType = $('#lpc-gradient-type').val();
        var gradientDirection = $('#lpc-gradient-direction').val();

        var gradientStyle = gradientType === 'radial' ? "radial-gradient(circle, #" + color1 + ", #" + color2 + ")" : "linear-gradient(" + gradientDirection + ", #" + color1 + ", #" + color2 + ")";

        $('#lpc-preview-background').css('background', gradientStyle);
    }

    // Initial preview update
    updatePreview();

    // Interval to continuously update preview
    setInterval(function() {
        updatePreview();
    }, 200); // Adjust interval as needed (e.g., every second)

});
