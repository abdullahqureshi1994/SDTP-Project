<?php

require_once 'vendor/autoload.php'; // Include TCPDF library

// Generate a dummy invoice number
function generateInvoiceNumber() {
    return 'INV' . mt_rand(1000, 9999);
}

// Generate a dummy invoice date (within the last 30 days)
function generateInvoiceDate() {
    return date('Y-m-d', strtotime('-' . mt_rand(1, 30) . ' days'));
}

// Generate a dummy customer name
function generateCustomerName() {
    $firstNames = ['John', 'Alice', 'Bob', 'Emma', 'Michael', 'Sophia'];
    $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia'];
    return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
}

// Generate a dummy invoice amount
function generateInvoiceAmount() {
    return number_format(mt_rand(100, 10000) / 100, 2);
}

// Generate a dummy invoice status
function generateInvoiceStatus() {
    return "Not Paid";
    $statuses = ['Paid', 'Pending', 'Overdue'];
    return $statuses[array_rand($statuses)];
}

// Generate a dummy bank details
function generateBankDetails() {
    return "Bank Name: XYZ Bank\nAccount Number: XXXXXXXX\nIFSC Code: XXXX1234";
}

// Generate a dummy invoice data
function generateInvoice($customer = null, $amount = null, $course_id = null, $email = null, $status = null, $date = null, $number = null) {
    $invoice = [
        'number' => $number ?? generateInvoiceNumber(),
        'date' => $date ?? generateInvoiceDate(),
        'customer' => $customer ?? generateCustomerName(),
        'email' => $email ?? strtolower(generateCustomerName()).'@gmail.com',
        'amount' => $amount ?? generateInvoiceAmount(),
        'status' => $status ?? generateInvoiceStatus(),
        'course_id' => uniqid(),
    ];
    return $invoice;
}

// Generate PDF invoice
function generatePDFInvoice($invoice) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Company');
    $pdf->SetTitle('Invoice ' . $invoice['number']);
    $pdf->SetSubject('Invoice');
    $pdf->SetKeywords('Invoice, Payment, PDF');

    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 10);

    $pdf->AddPage();

    // Invoice details
    $html = '<h1>Invoice ' . $invoice['number'] . '</h1>';
    $html .= '<p>Date: ' . $invoice['date'] . '</p>';
    $html .= '<p>Customer: ' . $invoice['customer'] . '</p>';
    $html .= '<p>Amount: $' . $invoice['amount'] . '</p>';
    $html .= '<p>Status: ' . $invoice['status'] . '</p>';

    // Payment options
    $html .= '<h2>Payment Options</h2>';
    $html .= '<p>Pay online: <a href="#">Online Payment Link</a></p>';
    $html .= '<p>Pay via bank transfer:</p>';
    $html .= '<pre>' . generateBankDetails() . '</pre>';

    $pdf->writeHTML($html, true, false, true, false, '');
    
    $upload_dir = wp_upload_dir();

    // Generate a unique filename for the PDF
    $pdf_filename = 'invoice_' . uniqid() . '.pdf';

    // Path to the uploads directory
    $uploads_path = $upload_dir['basedir'];
    $uploads_url = $upload_dir['baseurl'];

    // Full path to the PDF file in the uploads directory
    $pdf_full_path = $uploads_path . '/' . $pdf_filename;

    // Save the PDF file to the uploads directory
    $pdf->Output($pdf_full_path, 'F');

    return $uploads_url.'/'.$pdf_filename;
}

// Generate multiple dummy invoices
function generateInvoices($count = 10) {
    $invoices = [];
    for ($i = 0; $i < $count; $i++) {
        $invoices[] = generateInvoice();
    }
    return $invoices;
}

// Example usage:

?>
