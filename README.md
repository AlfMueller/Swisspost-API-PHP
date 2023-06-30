 * This document provides a SwissPostAPI class that allows you to generate an address label barcode
 * using the Swiss Post API. The class handles the authentication process, sends a POST request to the API,
 * and retrieves the generated barcode label data in ZPL format. The ZPL string is then displayed and can be
 * saved as a .zpl file. Additionally, the comment provides instructions on how to test the generated ZPL file
 * using the Labelary ZPL Viewer and how to send the ZPL file to a Zebra printer for printing the barcode label.
 *
 * To use this document, replace the "XXXXX" placeholders with your actual client ID and client secret,
 * and then create an instance of the SwissPostAPI class to generate the barcode label.


 ZPL file test online: 
 http://labelary.com/viewer.html (100x150mm 300dpi)
