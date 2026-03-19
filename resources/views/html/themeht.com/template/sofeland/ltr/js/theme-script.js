/* ------------------------------------------------
  Project:   Sofeland - Modern Software & SaaS Landing Page Template
  Author:    ThemeHt
------------------------------------------------ */
/* ------------------------
    Table of Contents

  1. Predefined Variables
  2. Preloader  
  3. FullScreen
  4. Counter
  5. Owl carousel
  6. Testimonial Carousel
  7. Magnific Popup
  8. Scroll to top
  9. Banner Section
  10. Fixed Header
  11. Scrolling Animation
  12. Text Color, Background Color And Image
  13. Contact Form
  14. ProgressBar
  15. Countdown
  16. Wow Animation
  17. HT Window load and functions
------------------------ */

"use strict";

(function ($) {

  /*------------------------------------
    HT Predefined Variables
  --------------------------------------*/
  const $window = $(window),
        $document = $(document),
        $body = $('body');

  /*------------------------------------
    HT PreLoader
  --------------------------------------*/
  function preloader() {
     $('#ht-preloader').fadeOut();
  }

  /*------------------------------------
    HT menu
  --------------------------------------*/
  function menu() {  
   $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
    if (!$(this).next().hasClass('show')) {
      $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
    }
    const $subMenu = $(this).next(".dropdown-menu");
    $subMenu.toggleClass('show');

    $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
      $('.dropdown-submenu .show').removeClass("show");
    });

    return false;
  });
  }

  /*------------------------------------
    HT Counter
  --------------------------------------*/
  function counter() {  
    $('.count-number').countTo({
      refreshInterval: 2
    });   
  }

  /*------------------------------------
    HT Owl Carousel
  --------------------------------------*/
  function owlcarousel() {
    $('.owl-carousel').each( function() {
      const $carousel = $(this);
      $carousel.owlCarousel({
          items : $carousel.data("items"),
          slideBy : $carousel.data("slideby"),
          center : $carousel.data("center"),
          loop : true,
          margin : $carousel.data("margin"),
          dots : $carousel.data("dots"),
          nav : $carousel.data("nav"),      
          autoplay : $carousel.data("autoplay"),
          autoplayTimeout : $carousel.data("autoplay-timeout"),
          navText : [ '<span class="bi bi-arrow-left"><span>', '<span class="bi bi-arrow-right"></span>' ],
          responsive: {
            0:{items: $carousel.data('xs-items') ? $carousel.data('xs-items') : 1},
            576:{items: $carousel.data('sm-items')},
            768:{items: $carousel.data('md-items')},
            1024:{items: $carousel.data('lg-items')},
            1200:{items: $carousel.data("items")}
          },
      });
    });
  }

  /*------------------------------------
    HT Testimonial Carousel
  --------------------------------------*/  
  function testimonialcarousel() {
      $('.testimonial-carousel').on('slide.bs.carousel', function (evt) {
        $('.testimonial-carousel .controls li.active').removeClass('active');
        $('.testimonial-carousel .controls li:eq('+$(evt.relatedTarget).index()+')').addClass('active');
      });
  }

  /*------------------------------------
    HT Magnific Popup
  --------------------------------------*/
  function magnificpopup() {
    $('.popup-gallery').magnificPopup({
        delegate: 'a.popup-img',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
          enabled: true,
          navigateByImgClick: true,
          preload: [0,1] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
          tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
          titleSrc: function(item) {
            return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
          }
        }
      });
    if ($(".popup-youtube, .popup-vimeo, .popup-gmaps").length > 0) {
         $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
              disableOn: 700,
              type: 'iframe',
              mainClass: 'mfp-fade',
              removalDelay: 160,
              preloader: false,
              fixedContentPos: false
        });
    }
  }

  /*------------------------------------
    HT Scroll to top
  --------------------------------------*/
  function scrolltop() {
    const pxShow = 300,
          goTopButton = $(".scroll-top");

    if ($(window).scrollTop() >= pxShow) goTopButton.addClass('scroll-visible');

    $(window).on('scroll', function () {
      if ($(window).scrollTop() >= pxShow) {
        if (!goTopButton.hasClass('scroll-visible')) goTopButton.addClass('scroll-visible');
      } else {
        goTopButton.removeClass('scroll-visible');
      }
    });

    $('.smoothscroll').on('click', function (e) {
      e.preventDefault();
      $('body,html').animate({ scrollTop: 0 }, 700);
      return false;
    });
  }


  /*------------------------------------
    HT Fixed Header
  --------------------------------------*/
  function fxheader() {
    $(window).on('scroll', function () {
      if ($(window).scrollTop() >= 100) {
        $('#site-header').addClass('fixed-header');
      } else {
        $('#site-header').removeClass('fixed-header');
      }
    });
  }

  /*------------------------------------
    HT Scrolling Animation
  --------------------------------------*/
  function scrolling() {
    $('.nav-item a[href*="#"]:not([href="#"]):not([href="#show"]):not([href="#hide"])').on('click', function() {
      if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
        let target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
          $('html,body').animate({
            scrollTop: target.offset().top
          }, 700);  // Smooth scroll duration
          return false;
        }
      }
    });

    // Closes responsive menu when a scroll trigger link is clicked
    $('.nav-item a[href*="#"]:not([href="#"])').on('click', function () {
      $('.navbar-collapse').collapse('hide');
    });   

    const sections = document.querySelectorAll("section");
    const navLinks = document.querySelectorAll(".navbar-nav .nav-link");

    window.addEventListener("scroll", () => {
      let current = "";
      sections.forEach(section => {
        const sectionTop = section.offsetTop - 70;  // adjust for nav height
        if (pageYOffset >= sectionTop) {
          current = section.getAttribute("id");
        }
      });

      navLinks.forEach(link => {
        link.classList.remove("active");
        if (link.getAttribute("href") === "#" + current) {
          link.classList.add("active");
        }
      });
    });
  }

  /*------------------------------------------
    HT Text Color, Background Color And Image
  ---------------------------------------------*/
  function databgcolor() {
      $('[data-bg-color]').each(function() {
       $(this).css('background-color', $(this).data('bg-color'));  
      });
      $('[data-text-color]').each(function() {
       $(this).css('color', $(this).data('text-color'));  
      });
      $('[data-bg-img]').each(function() {
       $(this).css('background-image', 'url(' + $(this).data("bg-img") + ')');
      });
  }

  /*------------------------------------
    HT Contact Form
  --------------------------------------*/
  function contactform() { 
      $('#contact-form').validator().on('submit', function (e) {
  if (!e.isDefaultPrevented()) {
    e.preventDefault();

    const url = "php/contact.html";

    $.ajax({
      type: "POST",
      url: url,
      data: $(this).serialize(),
      dataType: "json", // ensure it parses JSON properly
      success: function (data) {
        const messageAlert = 'alert-' + data.type;
        const messageText = data.message;

        const alertBox = `
          <div class="alert ${messageAlert} alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            ${messageText}
          </div>`;

        if (messageAlert && messageText) {
          $('#contact-form').find('.messages').html(alertBox).show().delay(2000).fadeOut('slow');
          $('#contact-form')[0].reset();
        }
      },
      error: function () {
        const alertBox = `
          <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            An error occurred. Please try again later.
          </div>`;
        $('#contact-form').find('.messages').html(alertBox).show().delay(2000).fadeOut('slow');
      }
    });
  }
});

  }

  /*------------------------------------
    HT ProgressBar
  --------------------------------------*/
  function progressbar () {
    const progressBar = $('.progress');
    if(progressBar.length) {
      progressBar.each(function () {
        const Self = $(this);
        // Make sure appear plugin is included or use alternative
        Self.appear(function () {
          const progressValue = Self.data('value');
          Self.find('.progress-bar').animate({
            width:progressValue+'%'           
          }, 1000);
        });
      });
    }
  }


  /*------------------------------------
    HT Countdown
  --------------------------------------*/
  function countdown() {
    $('.countdown').each(function () {
      const $this = $(this),
        finalDate = $(this).data('countdown');
      $this.countdown(finalDate, function (event) {
        $(this).html(event.strftime(
          '<li><span>%-D</span><p>Days</p></li>' +
          '<li><span>%-H</span><p>Hours</p></li>' +
          '<li><span>%-M</span><p>Minutes</p></li>' +
          '<li><span>%S</span><p>Seconds</p></li>'
        ));
      });
    });
  }

  /*------------------------------------
    HT Wow Animation
  --------------------------------------*/
  function wowanimation() {
      const wow = new WOW({
          boxClass: 'wow',
          animateClass: 'animated',
          offset: 0,
          mobile: false,
          live: true
      });
      wow.init();
  }

  /*------------------------------------
    HT Window load and functions
  --------------------------------------*/
  $(document).ready(function() {
      menu();
      owlcarousel();
      counter();
      testimonialcarousel();
      magnificpopup();
      scrolltop();
      fxheader();
      scrolling();
      databgcolor();
      contactform();
      progressbar();
      countdown();
  });

  $window.on('load', function() {
      preloader();
      wowanimation();
  });

})(jQuery);
