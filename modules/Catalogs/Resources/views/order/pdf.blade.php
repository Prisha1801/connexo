<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate PDF using jQuery</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div id="content">
        <h1>Sample PDF Content</h1>
        <p>This is a simple PDF content generated with jsPDF using jQuery.</p>
    </div>
    <button id="generatePDF">Generate PDF</button>

    <script>
        $(document).ready(function() {
            $('#generatePDF').on('click', function() {
                // Create a new jsPDF instance
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                // Get the HTML content to be added to the PDF
                const content = $('#content').html();

                // Add HTML content to the PDF
                doc.html(content, {
                    callback: function(doc) {
                        // Save the generated PDF
                        doc.save('sample.pdf');
                    },
                    x: 10,
                    y: 10
                });
            });
        });
    </script>
</body>
</html>
