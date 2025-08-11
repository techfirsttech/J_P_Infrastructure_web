<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=2.0">
    <title>Raw Material Datasheet - Satyam Industries</title>
    <style>
        /* Regular screen styles */
        body {
            font-family: Arial, sans-serif;

            margin: 0;
            padding: 10px;
            background-color: #f5f5f5;
            color: #333;
        }

        /* PDF-specific styles that ensure single page */
        .pdf-content {
            background-image: url("SATYAM INDUSTRIES LETTERHEAD_page-0001.jpg");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            width: 210mm;
            min-height: 297mm;
            max-height: 297mm;
            margin: 0 auto;
            /* background: white; */
            padding: 5mm;
            box-sizing: border-box;
            font-size: 10pt;
            line-height: 1.2;
            overflow: hidden;
        }

        .letterhead {
            /* background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); */
            color: transparent;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 5px 0;
            letter-spacing: 1px;
        }

        .company-tagline {
            font-size: 11px;
            margin-bottom: 10px;
            font-style: italic;
            opacity: 0.9;
        }

        .address {
            font-size: 10px;
            line-height: 1.3;
            margin-bottom: 5px;
        }

        .contact {
            font-size: 10px;
            font-weight: bold;
        }

        .document-container {
            background: white;
            padding: 0;
        }

        .document-title {
            text-align: center;
            color: #1e3c72;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 2px solid #2a5298;
            padding-bottom: 5px;
        }

        .material-title {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            background: #1e3c72;
            color: white;
            padding: 8px 15px;
            margin: 0 0 10px 0;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .designation-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }

        .designation-item {
            background: #f8f9fa;
            padding: 8px;
            border-radius: 3px;
            border-left: 3px solid #2a5298;
        }

        .designation-label {
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 2px;
            font-size: 12px;
        }

        .designation-value {
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            background: white;
            font-size: 12px;
        }

        th {
            background: #1e3c72;
            color: white;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        td {
            padding: 4px;
            text-align: center;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .element-name {
            font-weight: bold;
            color: #1e3c72;
        }

        .property-name {
            font-weight: bold;
            color: #1e3c72;
            text-align: left;
        }

        .casting-table td:first-child {
            text-align: left;
        }

        .balance-cell {
            text-align: center !important;
            font-weight: bold;
            font-style: italic;
            background-color: #e8f4fd !important;
        }

        .date-info {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 3px;
            font-size: 12px;
            color: #666;
        }

        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 8px;
        }

        /* Control buttons */
        .control-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 10px 15px;
            background: #1e3c72;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #2a5298;
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 2000;
            text-align: center;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #1e3c72;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media print {

            .control-buttons,
            .loading {
                display: none !important;
            }

            body {
                background: white;
                padding: 0;
            }

            .pdf-content {
                width: 100%;
                height: 100vh;
                max-height: none;
                padding: 1mm;
            }
        }
    </style>
</head>

<body>
    <div class="control-buttons">
        <button class="btn" onclick="generateSinglePagePDF()" id="pdfBtn">📄 Generate Single Page PDF</button>
        <button class="btn" onclick="printDocument()">🖨️ Print</button>
        <button class="btn" onclick="fitToPagePDF()">📄 Fit to Page PDF</button>
    </div>

    <div class="loading" id="loadingDiv">
        <div class="spinner"></div>
        <p>Generating Single Page PDF...</p>
    </div>

    <div class="pdf-content" id="content">
        <div class="letterhead">
            <h1 class="company-name">SATYAM INDUSTRIES</h1>
            <p class="company-tagline">Excellence in Precision Casting Solutions</p>

            <div class="address"><strong>Address:</strong> Ramnagar 4, Aji Industrial Area, 80 Feet Road, Behind Ajay Way Bridge GIDC Aji Industrial Estate, Rajkot 360002, Gujarat, India</div>
            <div class="contact"><strong>Contact:</strong> +91 96627 34062</div>
        </div>

        <div class="document-container">
            <h1 class="document-title">RAW MATERIAL DATASHEET</h1>
            <h2 class="material-title">Silicon Bronze - UNS C69300</h2>

            <div class="section">
                <div class="section-title">Material Designation</div>
                <div class="designation-grid">
                    <div class="designation-item">
                        <div class="designation-label">Standard</div>
                        <div class="designation-value">UNS (Unified Numbering System)</div>
                    </div>
                    <div class="designation-item">
                        <div class="designation-label">Designation</div>
                        <div class="designation-value">C69300</div>
                    </div>
                    <div class="designation-item">
                        <div class="designation-label">Common Name</div>
                        <div class="designation-value">Silicon Bronze / Copper-Silicon Alloy</div>
                    </div>
                    <div class="designation-item">
                        <div class="designation-label">Material Type</div>
                        <div class="designation-value">Copper-Silicon-Zinc Alloy</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Chemical Composition (% by weight)</div>
                <table>
                    <thead>
                        <tr>
                            <th>Element</th>
                            <th>Min %</th>
                            <th>Max %</th>
                            <th>Typical %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="element-name">Copper (Cu)</td>
                            <td>73.000</td>
                            <td>77.000</td>
                            <td>75.000</td>
                        </tr>
                        <tr>
                            <td class="element-name">Silicon (Si)</td>
                            <td>2.700</td>
                            <td>3.400</td>
                            <td>3.050</td>
                        </tr>
                        <tr>
                            <td class="element-name">Zinc (Zn)</td>
                            <td colspan="3" class="balance-cell">Remainder</td>
                        </tr>
                        <tr>
                            <td class="element-name">Tin (Sn)</td>
                            <td>-</td>
                            <td>0.200</td>
                            <td>0.100</td>
                        </tr>
                        <tr>
                            <td class="element-name">Nickel (Ni)</td>
                            <td>-</td>
                            <td>0.100</td>
                            <td>0.050</td>
                        </tr>
                        <tr>
                            <td class="element-name">Iron (Fe)</td>
                            <td>-</td>
                            <td>0.100</td>
                            <td>0.050</td>
                        </tr>
                        <tr>
                            <td class="element-name">Manganese (Mn)</td>
                            <td>-</td>
                            <td>0.100</td>
                            <td>0.050</td>
                        </tr>
                        <tr>
                            <td class="element-name">Lead (Pb)</td>
                            <td>-</td>
                            <td>0.090</td>
                            <td>0.045</td>
                        </tr>
                        <tr>
                            <td class="element-name">Phosphorus (P)</td>
                            <td>0.040</td>
                            <td>0.150</td>
                            <td>0.095</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <div class="section-title">Casting Properties</div>
                <table class="casting-table">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Property</th>
                            <th>Specification</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="property-name">Casting Method</td>
                            <td>Shell Molding</td>
                        </tr>
                        <tr>
                            <td class="property-name">Pouring Temperature</td>
                            <td>1050-1120°C</td>
                        </tr>
                        <tr>
                            <td class="property-name">Mold Temperature</td>
                            <td>200-350°C</td>
                        </tr>
                        <tr>
                            <td class="property-name">Fluidity</td>
                            <td>Excellent</td>
                        </tr>
                        <tr>
                            <td class="property-name">Hot Tearing Resistance</td>
                            <td>Excellent</td>
                        </tr>
                        <tr>
                            <td class="property-name">Shrinkage Allowance</td>
                            <td>1.8-2.2%</td>
                        </tr>
                        <tr>
                            <td class="property-name">Gas Absorption</td>
                            <td>Low</td>
                        </tr>
                        <tr>
                            <td class="property-name">Corrosion Resistance</td>
                            <td>Excellent (Marine Applications)</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="date-info">
                <div><strong>Document Version:</strong> 2.1</div>
                <div><strong>Issue Date:</strong> August 2025</div>
                <div><strong>Valid Until:</strong> August 2026</div>
            </div>

            <div class="footer">
                <p><strong>Note:</strong> This datasheet is provided for informational purposes. Actual properties may vary based on casting conditions and specific application requirements.</p>
            </div>
        </div>
    </div>

    <!-- Load html2pdf.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        // Show loading indicator
        function showLoading() {
            document.getElementById('loadingDiv').style.display = 'block';
            document.getElementById('pdfBtn').disabled = true;
        }

        // Hide loading indicator
        function hideLoading() {
            document.getElementById('loadingDiv').style.display = 'none';
            document.getElementById('pdfBtn').disabled = false;
        }

        // Generate single page PDF with optimized settings
        async function generateSinglePagePDF() {
            try {
                showLoading();

                const element = document.getElementById('content');
                const today = new Date().toISOString().split('T')[0];

                const options = {
                    margin: [0, 0, 0, 0], // Small margins in mm
                    filename: `Satyam_Industries_Datasheet_ECO_Brass_${today}.pdf`,
                    image: {
                        type: 'jpeg',
                        quality: 1
                    },
                    html2canvas: {
                        scale: 2, // Reduced scale for better fitting
                        useCORS: true,
                        letterRendering: true,
                        logging: false,
                        allowTaint: true,
                        backgroundColor: '#ffffff',
                        width: 794, // A4 width in pixels at 96 DPI
                        height: 1120, // A4 height in pixels at 96 DPI
                        scrollX: 0,
                        scrollY: 0
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait',
                        compress: true,
                        precision: 16
                    },
                    pagebreak: {
                        mode: 'avoid-all' // Avoid page breaks
                    }
                };

                await html2pdf().set(options).from(element).save();

            } catch (error) {
                console.error('PDF generation error:', error);
                alert('Error generating PDF. Please try again.');
            } finally {
                hideLoading();
            }
        }

        // Alternative method - Fit to page by scaling content
        async function fitToPagePDF() {
            try {
                showLoading();

                const element = document.getElementById('content');

                // Temporarily adjust content for single page fit
                const originalStyle = element.style.cssText;
                element.style.transform = 'scale(2)';
                element.style.transformOrigin = 'top left';
                element.style.width = '125%'; // Compensate for scale

                const options = {
                    margin: [0, 0, 0, 0], // Very small margins
                    filename: 'Satyam_Datasheet_FitToPage.pdf',
                    image: {
                        type: 'jpeg',
                        quality: 1
                    },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        letterRendering: true,
                        logging: false,
                        allowTaint: true,
                        backgroundColor: '#ffffff',
                        windowWidth: 1024,
                        windowHeight: 1400
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait',
                        compress: true
                    },
                    pagebreak: {
                        mode: 'avoid-all'
                    }
                };

                await html2pdf().set(options).from(element).save();

                // Restore original styling
                element.style.cssText = originalStyle;

            } catch (error) {
                console.error('PDF generation error:', error);
                alert('Error generating PDF. Please try again.');

                // Restore original styling in case of error
                const element = document.getElementById('content');
                element.style.cssText = '';
            } finally {
                hideLoading();
            }
        }

        // Generate PDF using canvas approach for precise control
        async function generateCanvasPDF() {
            try {
                showLoading();

                const element = document.getElementById('content');

                // Use html2canvas directly for better control
                const canvas = await html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    width: element.scrollWidth,
                    height: element.scrollHeight,
                    windowWidth: 1024,
                    windowHeight: 1400
                });

                // Create PDF with exact canvas dimensions
                const imgData = canvas.toDataURL('image/jpeg', 1);
                const {
                    jsPDF
                } = window.jspdf;

                // Calculate dimensions to fit A4
                const a4Width = 210; // mm
                const a4Height = 297; // mm
                const canvasRatio = canvas.height / canvas.width;

                let pdfWidth = a4Width - 0; // 10mm margin on each side
                let pdfHeight = pdfWidth * canvasRatio;

                // If height exceeds A4, scale down
                if (pdfHeight > a4Height - 0) {
                    pdfHeight = a4Height - 0;
                    pdfWidth = pdfHeight / canvasRatio;
                }

                const pdf = new jsPDF('p', 'mm', 'a4');
                pdf.addImage(imgData, 'JPEG', 10, 10, pdfWidth, pdfHeight);
                pdf.save('Satyam_Datasheet_Canvas.pdf');

            } catch (error) {
                console.error('Canvas PDF generation error:', error);
                alert('Error generating PDF. Please try again.');
            } finally {
                hideLoading();
            }
        }

        // Regular print function
        function printDocument() {
            window.print();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Single Page PDF Datasheet loaded');

            // Add keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    generateSinglePagePDF();
                }
            });
        });

        // Add canvas method to buttons
        document.addEventListener('DOMContentLoaded', function() {
            const controlButtons = document.querySelector('.control-buttons');
            const canvasBtn = document.createElement('button');
            canvasBtn.className = 'btn';
            canvasBtn.innerHTML = '🎨 Canvas PDF';
            canvasBtn.onclick = generateCanvasPDF;
            controlButtons.appendChild(canvasBtn);
        });
    </script>
</body>

</html>