<?php
$contactInfo = loadJsonData('contact');
?>

<section class="contact-us">
    <div class="container">
        <h1><?php echo t('contact_us_title'); ?></h1>
        <div class="contact-content">
            <div class="contact-form">
                <h2><?php echo t('send_us_message'); ?></h2>
                <form id="contact-form" action="process-contact.php" method="POST">
                    <div class="form-group">
                        <label for="name"><?php echo t('name'); ?></label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><?php echo t('email'); ?></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject"><?php echo t('subject'); ?></label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message"><?php echo t('message'); ?></label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <button type="submit" class="button"><?php echo t('send_message'); ?></button>
                </form>
            </div>
            <div class="contact-info">
                <h2><?php echo t('contact_information'); ?></h2>
                <p><?php echo $contactInfo['address']; ?></p>
                <p><?php echo t('phone'); ?>: <?php echo $contactInfo['phone']; ?></p>
                <p><?php echo t('email'); ?>: <?php echo $contactInfo['email']; ?></p>
                <div id="map" class="contact-map"></div>
            </div>
        </div>
    </div>
</section>

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&callback=initMap" async defer></script>
<script>
function initMap() {
    const officeLocation = { lat: <?php echo $contactInfo['lat']; ?>, lng: <?php echo $contactInfo['lng']; ?> };
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: officeLocation,
    });
    const marker = new google.maps.Marker({
        position: officeLocation,
        map: map,
    });
}
</script>

