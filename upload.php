<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload SVG</title>
    <style>
        .color-picker {
            margin: 10px 0;
        }
        canvas {
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <form id="uploadForm" action="upload.php" method="post" enctype="multipart/form-data">
        <label for="file">Choose SVG file:</label>
        <input type="file" name="file" id="file" accept=".svg">
        <div id="colorPickers"></div>
        <input type="submit" value="Upload and Change Colors">
    </form>
    <canvas id="svgCanvas" width="500" height="500"></canvas>
    <script>
        document.getElementById('file').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file && file.type === 'image/svg+xml') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const parser = new DOMParser();
                    const svgDoc = parser.parseFromString(e.target.result, 'image/svg+xml');
                    const colors = new Set();
                    svgDoc.querySelectorAll('[fill]').forEach(element => {
                        colors.add(element.getAttribute('fill'));
                    });
                    const colorPickersDiv = document.getElementById('colorPickers');
                    colorPickersDiv.innerHTML = '';
                    colors.forEach(color => {
                        const colorPicker = document.createElement('input');
                        colorPicker.type = 'color';
                        colorPicker.value = color;
                        colorPicker.className = 'color-picker';
                        colorPicker.dataset.originalColor = color;
                        colorPickersDiv.appendChild(colorPicker);

                        colorPicker.addEventListener('input', function() {
                            const newColor = this.value;
                            svgDoc.querySelectorAll(`[fill="${this.dataset.originalColor}"]`).forEach(element => {
                                element.setAttribute('fill', newColor);
                            });
                            this.dataset.originalColor = newColor;
                            updateCanvas(svgDoc);
                        });
                    });
                    updateCanvas(svgDoc);
                };
                reader.readAsText(file);
            }
        });

        function updateCanvas(svgDoc) {
            const canvas = document.getElementById('svgCanvas');
            const ctx = canvas.getContext('2d');
            const svgData = new XMLSerializer().serializeToString(svgDoc);
            const img = new Image();
            img.onload = function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);
            };
            img.src = 'data:image/svg+xml;base64,' + btoa(svgData);
        }
    </script>
</body>
</html>
