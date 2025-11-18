<?php
require '../_base.php';

$_title = 'KAWAII.Brand Profile';
include '../_head.php';
?>


<style>
    .brand-profile {
        font-family: 'Playpen Sans', serif;
        background-color: #fff4f4;
        padding: 80px 20px;
        text-align: center;
        color: #555;
        position: relative;
        margin-top: -65px;
    }

    .brand-profile h1 {
        font-size: 42px;
        color: #e58f8f;
        margin-bottom: 15px;
    }

    .brand-profile .subtitle {
        font-size: 20px;
        color: #666;
        margin-bottom: 40px;
    }

    .brand-story {
        max-width: 1000px;
        margin: 0 auto;
        font-size: 19px;
        line-height: 1.9;
        text-align: left;
        padding: 0 20px;
        position: relative;
        z-index: 2;
    }

    .brand-story p {
        margin-bottom: 25px;
    }

    .heart-icon {
        color: #e58f8f;
        font-weight: 600;
        text-align: center;
    }

    .float-deco {
        position: absolute;
        width: 60px;
        opacity: 0.2;
        animation: float 6s ease-in-out infinite;
    }

    .float-deco.one {
        top: 20px;
        left: 10%;
        animation-delay: 0s;
    }

    .float-deco.two {
        bottom: 40px;
        left: 80%;
        animation-delay: 2s;
    }

    .float-deco.three {
        top: 100px;
        right: 15%;
        animation-delay: 4s;
    }

    .brand-story p {
        text-align: justify;
    }

    .founder-section {
        display: flex;
        align-items: center;
        gap: 25px;
        margin-top: 10px;
        background-color: #ffe9e9;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 5px 15px rgba(255, 175, 175, 0.2);
        flex-wrap: wrap;
        margin-bottom: 40px;
    }

    .founder-photo {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #ffb6c1;
        box-shadow: 0 4px 10px rgba(255, 182, 193, 0.4);
    }

    .founder-text {
        flex: 1;
        font-size: 18px;
        color: #444;
        font-family: 'Playpen Sans', cursive;
        margin-bottom: -40px;
    }

    .founder-text h3 {
        font-size: 24px;
        color: #e58f8f;
        margin-bottom: -10px;
        margin-top: -10px;
        font-weight: bold;
    }
</style>

<div class="brand-profile">
    <h1>About KAWAII. Plushies</h1>
    <p class="subtitle">Where cuteness meets comfort, and every plushie tells a story...</p>

    <div class="brand-story">
        <div class="founder-section">
            <img src="/profile/ggbond.jpeg" alt="Founder Cat" class="founder-photo">
            <div class="founder-text">
                <h3>Our Founder</h3>
                <p><strong>GG Bonddd</strong> aka JuJu — our fluffy, whiskered CEO. With a heart full of cuddles and a dream of spreading joy, GG bond started KAWAII. Plushies from his cozy nap corner. His goal? To bring cuteness, comfort, and cat-approved plushies to the world, one paw at a time.</p>
                <p>He still attends all creative meetings (mostly for snacks) and insists on personally hugging every new plushie before it joins our store!</p>
            </div>
        </div>
        <p><strong>KAWAII. Plushies</strong> started with a simple belief: that the world needs more softness — not just in the things we touch, but in the way we live. That's why we create plushies that aren't just adorable, but meaningful. Every plushie is designed with love, care, and a little sprinkle of magic 🌈</p>

        <p>Our brand was founded in Malaysia, where we dreamed of creating a cozy space filled with charm and joy. Inspired by Japanese kawaii culture, our plushies are full of character, pastel colors, and sweet little stories of their own. Whether it's a sleepy bear, a smiling dumpling, or a blushing sea creature, each one has a heart of gold (and stuffing made with love!).</p>

        <p>We believe plushies aren't just toys — they're companions. They're gifts of comfort when someone needs a hug. They're the surprise that makes someone's birthday unforgettable. They're keepsakes from a friend, or a soft reminder that you're loved.</p>

        <p>We're proud to bring our plushie family to life online, so you can enjoy cuteness wherever you are. From browsing to unboxing, we want your experience to feel like a warm cup of cocoa on a rainy day ~</p>

        <p>You can also find us in-person at our boutique store in <strong>Pavilion Kuala Lumpur</strong> — Level 6, right beside the dessert café. It's our little pink paradise, and we'd love to welcome you for a visit!</p>

        <p class="heart-icon">We're so happy you're here - welcome to the cutest corner of the internet! 🧸🌸</p>
    </div>
</div>

<?php
include '../_foot.php';
