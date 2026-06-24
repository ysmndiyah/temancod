<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
$pageTitle = 'Informasi Layanan';
include '../includes/header.php';
?>
<div class="page-hero">
    <div class="page-hero-content">
        <h1>Harga Fleksibel Sesuai Companion</h1>
        <p>Setiap companion memiliki tarif per jam yang berbeda. Pilih companion sesuai kebutuhan dan durasi pendampingan.</p>
        <div class="hero-buttons">
            <a href="companions.php" class="btn btn-primary">Temukan Companion Sekarang</a>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <span class="section-label">Cara Kerja</span>
        <h2>Alur Layanan TemanCOD</h2>
        <p>Proses yang sederhana dan jelas dari pencarian companion sampai pendampingan selesai.</p>
    </div>
    <div class="steps-grid flow-grid">
        <div class="step-card">
            <div class="step-num">1</div>
            <div class="step-icon">🔎</div>
            <h3>Cari Companion</h3>
            <p>Pilih companion yang tersedia sesuai lokasi, jadwal, dan kebutuhan COD kamu.</p>
        </div>
        <div class="step-card">
            <div class="step-num">2</div>
            <div class="step-icon">📝</div>
            <h3>Isi Detail COD</h3>
            <p>Isi tanggal, jam, alamat jemput, lokasi COD, dan durasi pendampingan agar admin bisa menyiapkan layanan.</p>
        </div>
        <div class="step-card">
            <div class="step-num">3</div>
            <div class="step-icon">💳</div>
            <h3>Transfer Pembayaran</h3>
            <p>Setelah mengisi detail, customer melakukan pembayaran ke rekening admin TemanCOD sesuai tarif companion yang dipilih.</p>
        </div>
        <div class="step-card">
            <div class="step-num">4</div>
            <div class="step-icon">📲</div>
            <h3>Verifikasi dan Hubungi Companion</h3>
            <p>Admin memverifikasi pembayaran lalu menghubungi companion melalui WhatsApp untuk memastikan jadwal dan koordinasi berjalan lancar.</p>
        </div>
        <div class="step-card">
            <div class="step-num">5</div>
            <div class="step-icon">✅</div>
            <h3>Pendampingan Selesai</h3>
            <p>Companion mendampingi proses COD sampai selesai, dan customer bisa melanjutkan aktivitas dengan aman.</p>
        </div>
    </div>
</div>

<div class="section section-alt">
    <div class="section-header">
        <span class="section-label">Simulasi Tarif</span>
        <h2>Tarif yang Bisa Kamu Pilih</h2>
        <p>Setiap companion memiliki tarif per jam yang berbeda sesuai kebutuhan pendampingan.</p>
    </div>
    <div class="pricing-grid">
        <div class="card pricing-card">
            <div class="card-body">
                <div class="pricing-tier">Companion Reguler</div>
                <div class="pricing-amount">Rp35.000<span>/jam</span></div>
                <ul class="pricing-list">
                    <li>1 jam = Rp35.000</li>
                    <li>2 jam = Rp70.000</li>
                    <li>3 jam = Rp105.000</li>
                </ul>
            </div>
        </div>
        <div class="card pricing-card">
            <div class="card-body">
                <div class="pricing-tier">Companion Standar</div>
                <div class="pricing-amount">Rp50.000<span>/jam</span></div>
                <ul class="pricing-list">
                    <li>1 jam = Rp50.000</li>
                    <li>2 jam = Rp100.000</li>
                    <li>3 jam = Rp150.000</li>
                </ul>
            </div>
        </div>
        <div class="card pricing-card">
            <div class="card-body">
                <div class="pricing-tier">Companion Premium</div>
                <div class="pricing-amount">Rp75.000<span>/jam</span></div>
                <ul class="pricing-list">
                    <li>1 jam = Rp75.000</li>
                    <li>2 jam = Rp150.000</li>
                    <li>3 jam = Rp225.000</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="pricing-grid note-grid">
        <div class="card note-card">
            <div class="card-body">
                <h3>Pembayaran dilakukan ke admin TemanCOD</h3>
                <p>Customer membayar biaya layanan ke rekening admin TemanCOD. Setelah pembayaran masuk, admin akan memproses permintaan dan memulai tahap verifikasi.</p>
            </div>
        </div>
        <div class="card note-card">
            <div class="card-body">
                <h3>Verifikasi dan penghubungan via WhatsApp</h3>
                <p>Setelah pembayaran diverifikasi, admin akan menghubungi companion melalui WhatsApp dan menginformasikan detail order. Customer tidak melakukan pembayaran langsung kepada companion.</p>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>