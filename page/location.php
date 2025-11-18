<?php
require '../_base.php';

$_title = 'KAWAII.Location';
include '../_head.php';
?>

<style>
    .location-page {
        font-family: 'Playpen Sans', serif;
        text-align: center;
        padding: 50px 20px;
        background: #fff4f4;
        color: #444;
        margin-top: -65px;
    }

    .location-page h1 {
        font-size: 42px;
        color: #e58f8f;
        margin-bottom: 10px;
    }

    .location-page p.subtitle {
        font-size: 20px;
        color: #666;
        margin-bottom: 30px;
    }

    .location-box {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        max-width: 900px;
        margin: 0 auto;
    }

    .location-info {
        margin-bottom: 20px;
        line-height: 1.8;
        font-size: 18px;
        text-align: left;
    }

    .map-container {
        width: 100%;
        height: 400px;
        border-radius: 15px;
        overflow: hidden;
        margin-top: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
</style>

<div class="location-page">
    <div class="container">
        <h1>Our Store Location</h1>
        <p class="subtitle">Drop by and visit our adorable little store — we'd love to see you!</p>

        <div class="location-box">
            <div class="location-info">
                <strong>KAWAII. Plushies</strong><br>
                Lot 6.45, Level 6, Pavilion Kuala Lumpur,<br>
                168 Jalan Bukit Bintang, 55100 Kuala Lumpur, Malaysia<br>
                <p style="color:#777; font-size:16px;">📍 Find us on Level 6, right next to the yummiest dessert café</p>
            </div>

            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.794049400765!2d101.7108375749398!3d3.1489660531538495!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc362c8935509b%3A0xed966c50b0a79fb4!2sPavilion%20Kuala%20Lumpur!5e0!3m2!1sen!2smy!4v1745414701436!5m2!1sen!2smy" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include '../_foot.php'; ?>