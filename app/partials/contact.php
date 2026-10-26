<section class="contact-section" id="contact" aria-labelledby="contact-title">
  <div class="contact-section__inner">
    <div class="contact-section__eyebrow"><span aria-hidden="true">•</span> CONTACT</div>
    <h2 class="contact-section__title" id="contact-title">Contact us for amazing <br>video editing projects</h2>
    <div class="contact-card">
      <form class="contact-form" action="<?= e(asset('../contact-submit.php')) ?>" method="post" data-contact-form>
        <div class="contact-form__row">
          <label class="contact-field"><span class="contact-field__label">Full Name</span><input type="text" name="name" placeholder="Your Name" autocomplete="name" required></label>
          <label class="contact-field"><span class="contact-field__label">Email Address</span><input type="email" name="email" placeholder="yourname@business.com" autocomplete="email" required></label>
        </div>
        <label class="contact-field contact-field--service"><span class="contact-field__label">Service</span><span class="contact-select-wrap"><select name="service" aria-label="Service" required><option value="" selected disabled>Select...</option><option>Short-form Video Editing</option><option>Long-form Video Editing</option><option>Motion Graphics</option><option>VFX</option><option>Podcast Editing</option></select></span></label>
        <label class="contact-field contact-field--message"><span class="contact-field__label">Message</span><textarea name="message" placeholder="Please provide a detailed description of your request" required></textarea></label>
        <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px">
        <div class="contact-form__actions"><button class="contact-form__submit" type="submit">Submit Your Request</button><p>For same-day reservations or special <br>requests, feel free to give us a call!</p></div>
        <p class="contact-form__status" data-contact-status aria-live="polite"></p>
        <a class="contact-form__email" href="mailto:Contact@oxynstudio.com"><span class="contact-form__email-icon" aria-hidden="true">✉</span><span>Contact@oxynstudio.com</span></a>
      </form>
      <div class="contact-card__visual" aria-hidden="true"><video autoplay muted loop playsinline preload="metadata"><source src="<?= e(asset('videos/contact-video.mp4')) ?>" type="video/mp4"></video></div>
    </div>
  </div>
</section>
