/**jshint esversion: 6 */
import './_foundation';

import { nanoid } from 'nanoid';

import random from "lodash/random";
import isEmpty from "lodash/isEmpty";
import toString from "lodash/toString";

/** current-device */
import device from "current-device";
const is_mobile = () => device.mobile();
const is_tablet = () => device.tablet();
const is_desktop = () => device.desktop();

/** Fancybox */
import { Fancybox } from "@fancyapps/ui";
Fancybox.bind(".wp-block-gallery .wp-block-image a, [id^=\"gallery-\"] a", {
    groupAll: true, // Group all items
});

Fancybox.bind(".fcy-popup, .fcy-video, .banner-video a", {});

const $ = jQuery;

/** Create deferred YT object */
const YTdeferred = $.Deferred();
window.onYouTubeIframeAPIReady = function () {
    YTdeferred.resolve(window.YT);
};

/** AOS */
// import AOS from 'aos';
// AOS.init();

/** video.js */
import videojs from "!video.js";
import videojs_youtube from "videojs-youtube/dist/Youtube.min";

/** jquery */
$(() => {

    //...
    // $('body').on('DOMSubtreeModified', '.wpcf7-response-output', function () {
    //     setTimeout(function () {
    //         $('.wpcf7-response-output').fadeOut('slow');
    //     }, 5000);
    // });

    //...
    let accordion_outer = $('.accordion-outer');
    if ( is_mobile() ) {
        accordion_outer.find('ul ~ ul > li.accordion-item').removeClass('is-active');
    }

    //...
    // let single_content_iframe = $(".single-content .content iframe");
    // single_content_iframe.wrapAll('<span class="cover res ar-16-9"></span>');

    // let single_content_iframe = $(".single-content .content iframe");
    // single_content_iframe.wrapAll('<span class="cover res ar-16-9"></span>');

    let single_content_iframe = $(".single-content .content iframe");
    single_content_iframe.each(function() {
        $(this).wrap('<span class="cover res ar-16-9"></span>');
    });

    // ...
    let swiper_initialized = $('.swiper.swiper-initialized');
    swiper_initialized.closest('.swiper-section').addClass('swiper-section-initialized');

    // tabs
    const tabs_wrapper = $(".w-filter-tabs");
    tabs_wrapper.each((index, el) => {
        rand_element_init(el);
        let _id = $(el).attr('id');

        const _nav = $(el).find(".tabs-nav");
        const _content = $(el).find(".tabs-content");

        _content.find('.tabs-panel').hide();
        let _cookie = 'cookie_' + _id + '_' + index;

        if (getCookie(_cookie) === '' || getCookie(_cookie) === 'undefined') {
            let _hash = _nav.find('a:first').attr("href");
            setCookie(_cookie, _hash, 100);
        }

        _nav.find('a[href="' + getCookie(_cookie) + '"]').addClass("current").closest('li').addClass("active");
        _nav.find('a').on("click", function (e) {

            e.preventDefault();
            setCookie(_cookie, $(this).attr("href"), 100);

            _nav.find('a.current').removeClass("current").closest('li').removeClass("active")
            _content.find('.tabs-panel:visible').removeClass('show').hide();

            $(this.hash).addClass("show").fadeIn();
            $(this).addClass("current").closest('li').addClass("active");

            //...
            let grid_products = $('.grid-products');
            grid_products.each((index, el) => {
                let product_button = $(el).find('a.product_button');
                let _position_top = product_button.closest('article.item').innerWidth();
                product_button.css({'top': _position_top - 40});
            });

        }).filter(".current").trigger('click');

        //...
        let check_current = _nav.find('a.current');
        if (check_current.length > 0) {}
        else {
            _nav.find('a:first').addClass("current").trigger('click');
        }
    });

    /** */
    const footerDropdownBtns = Array.from(document.querySelectorAll(".footer-widget .widget_nav_menu .widget-title"));
    const footerDropdownContent = Array.from(document.querySelectorAll(".footer-widget .widget_nav_menu"));
    var vw = window.screen.width;
    if (vw < 641) {
        footerDropdownBtns.forEach((item, i) => item.addEventListener("click", () => {
            $(footerDropdownContent[i].lastElementChild).find('ul.menu').slideToggle();
            $(footerDropdownContent[i].firstElementChild).toggleClass("open");
        }));
    }

    /** */
    let item_product = $('article.product');
    item_product.find('span.res').find('img + img').removeClass('wvs-archive-product-image');

    // https://stackoverflow.com/questions/68605784/animated-counter-when-scrolling-into-viewport-with-thousands-separator
    const animNum = (EL) => {
        if (EL._isAnimated) return; // Animate only once!
        EL._isAnimated = true;

        $(EL).prop('Counter', 0).animate({
            Counter: EL.dataset.counter
        }, {
            duration: 2000,
            step: function(now) {
                const text = (Math.ceil(now)).toLocaleString('en-US');
                const html = text.split(",").map(n => `<span class="count">${n}</span>`).join(",");
                $(this).html(html);
            }
        });
    };

    const inViewport = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) animNum(entry.target);
        });
    };

    $("[data-counter]").each((i, EL) => {
        const observer = new IntersectionObserver(inViewport);
        observer.observe(EL);
    });

    /** */
    $('a.mega-menu-link').on( 'mouseenter', function (e) {

        let thumb_src = $(this).find('.menu-thumb').data('thumb');
        let caption = $(this).find('.menu-thumb').data('caption');
        let description = $(this).find('.menu-thumb').data('description');

        let li_mega_menu = $(this).closest('li.mega-menu-megamenu')
        let media_thumb = li_mega_menu.find('.mega-media-thumb');

        if ( 'undefined' !== thumb_src && thumb_src ) {
            if ( ! $(this).hasClass("hover")) {
                media_thumb.find('figure a.thumb').hide().html('<img src="' + thumb_src + '" alt="' + caption + '" />').fadeIn();
                if ( 'undefined' !== caption ) {
                    media_thumb.find('.wp-caption-text').find('.caption').html(caption);
                }

                if ( 'undefined' !== description ) {
                    media_thumb.find('.wp-caption-text').find('.desc').html(description);
                }

                $('a.mega-menu-link').removeClass('hover');
                $(this).addClass('hover');
            }
        }
    });

    /** */
    $('.product-pos-item').on('click', function(e) {
        $(this).toggleClass('active');
    });

    /** */
    $(".product-detail-inner .saleoff.onsale").prependTo(".product-detail-inner .woocommerce-product-gallery");

    /**
     * @param el
     * @return void
     */
    function rand_element_init(el) {
        const _rand = nanoid(9);
        $(el).addClass(_rand);

        let _id = $(el).attr('id');
        if (!_id) {
            _id = _rand;
            $(el).attr('id', _id);
        }
    }

    /** */
    $(document).on('click','ul.tabs-heading a',function(e) {
        $(this).closest('.tabs-wrapper').find('.desc-inner').removeAttr("style");
        $(this).closest('.tabs-wrapper').find('.viewmore-wrapper').fadeOut();
    });

    /** */
    $(document).on('click','.woocommerce.widget_price_filter>span:first-child, .woocommerce.widget_layered_nav>span:first-child',function(e) {
        e.preventDefault();
        $(this).toggleClass('is-active');
    });

    /** */
    $('.variations_form').each(function () {

        // when variation is found, do something
        $(this).on('found_variation', function (event, variation) {
            if (variation.price_html !== '') {
                $(".product-detail-inner p.single-price").html(variation.price_html);
            }
        });
    });

    /** */
    let video_js = $('.video-js');
    video_js.each((index, el) => {
        const _rand = nanoid(8);
        $(el).attr('id', 'video_js_' + _rand);

        let options = { muted: true };
        if ( $(el).hasClass("autoplay")) {
            options = { muted: true, autoplay: true };
        }

        videojs( 'video_js_' + _rand, options );
    });

    // youtube
    const ng_iframe_load = () => {
        let _ng_iframe_wrap = $("#ng-iframe--wrap");
        if (_ng_iframe_wrap.length) {

            // https://developers.google.com/youtube/iframe_api_reference#Getting_Started
            let _tag = document.createElement("script");
            _tag.src = "https://www.youtube.com/iframe_api";
            let _firstScriptTag = document.getElementsByTagName("script")[0];
            _firstScriptTag.parentNode.insertBefore(_tag, _firstScriptTag);

            let youTubeUrl = _ng_iframe_wrap.data('embed');
            let _regExp = /https:\/\/www\.youtube\.com\/watch\?v=([\w-]{11})/;
            let youTubeId = youTubeUrl.match(_regExp)[1];
            if (11 === youTubeId.length) {
                let player;

                // Failed to execute 'postMessage' on 'DOMWindow'
                // https://stackoverflow.com/a/47914456
                window['YTConfig'] = {'host': 'https://www.youtube.com'};

                // whenever youtube callback was called = deferred resolved your custom function will be executed with YT as an argument
                YTdeferred.done(function (YT) {
                    let _playerVars = {
                        html5: 1,
                        autoplay: 1,
                        autohide: 1,
                        playsinline: 1,
                        rel: 0,
                        fs: 0,
                        version: 3,
                        loop: 1,
                        controls: 0,
                        enablejsapi: 1,
                        cc_load_policy: 1,
                        iv_load_policy: 3,
                        modestbranding: 1,
                        playlist: youTubeId,
                        origin: window.location.origin,
                        playerapiid: 'ng-iframe--wrap',
                    };

                    // https://developers.google.com/youtube/iframe_api_reference#Getting_Started
                    player = new YT.Player('ng-iframe--wrap', {
                        videoId: youTubeId,
                        playerVars: _playerVars,
                        allowfullscreen: 1,
                        events: {
                            'onReady': _onPlayerReady,
                            'onStateChange': _onPlayerStateChange
                        },
                    });
                });
            }
        }
    }

    /**
     * @param event
     * @private
     */
    function _onPlayerReady(event) {
        event.target.mute();
        event.target.playVideo();
    }

    /**
     * @param el
     * @private
     */
    function _onPlayerStateChange(el) {
        const _loader_overlay = $('#loader-overlay');
        if (el.data === 1) {
            _loader_overlay.fadeOut(2000);
        } else {
            _loader_overlay.fadeIn(300);
        }
    }

    /** */
    let product_desc = $('.product-desc-inner');
    product_desc.each((index, el) => {
        const _rand = nanoid(8);
        $(el).attr('id', 'desc_' + _rand);

        let _height = $(el).outerHeight(false);
        if (_height > 1100) {
            $(el).css({'height':'1000px','transition':'0.3s'});
            let _viewmore_html = '<div class="viewmore-wrapper"><a class="btn-viewmore" title="' + HD.lg.view_more + '" data-src="#desc_' + _rand + '" data-modal="false" href="javascript:;" data-glyph-after="">' + HD.lg.view_more + '</a></div>';
            $(el).append(_viewmore_html);

            $("#desc_" + _rand).find('.btn-viewmore').on('click', function (e) {
                e.preventDefault();

                //$(el).addClass('is-open');
                $(el).css({'height':'auto','transition':'0.3s'});
                $("#desc_" + _rand).find('.viewmore-wrapper').fadeOut();
            })

            //Fancybox.bind("#desc-inner .btn-viewmore", {});
        }
    });

    /** */
    $(document).on('click', '.mega-menu-title',function(e) {
        e.preventDefault();
        $(this).toggleClass('is-active');
    });

    /** */
    let reveal_contact_popup = $('.reveal-contact-popup');
    if (reveal_contact_popup.length > 0) {
        reveal_contact_popup.find('.close-button').on('click', function (e) {
            e.preventDefault();
            $(this).closest('.reveal-contact-popup').fadeOut();
        });
    }

    let contact_popup = $('.contact-popup');
    if (contact_popup.length > 0) {
        contact_popup.on('click', function (e) {
            e.preventDefault();
            if (reveal_contact_popup.length > 0) {
                reveal_contact_popup.fadeIn();
            }
        });
    }

    /** Remove empty P tags created by WP inside of Accordion and Orbit */
    $('.accordion p:empty, .orbit p:empty').remove();

    /** toggle menu */
    const _toggle_menu = $(".toggle_menu");
    _toggle_menu.find("li.is-active.has-submenu-toggle").find(".submenu-toggle").trigger('click');

    /** */
    const wpg__image = $('.wpg__image');
    wpg__image.find('a').on('click', function (e) {
        e.preventDefault();
        $(this).next('.image-popup').trigger('click');
    });

    /** */
    const wpg__thumb = $('.wpg__thumb');
    wpg__thumb.find('a').on('click', function (e) {
        e.preventDefault();
    });

    /** */
    const _qty_controls = () => {

        /**qty*/
        $('.input-number-increment').off('click').on('click', function (e) {
            e.preventDefault();
            let $input = $(this).parents('.input-number-group').find('.qty');
            let val = parseInt($input.val(), 10);
            $input.val(val + 1);

            let update_cart = $('button[name="update_cart"]');
            if (update_cart.length > 0) {
                update_cart.prop('disabled', false)
            }
        });

        $('.input-number-decrement').off('click').on('click', function (e) {
            e.preventDefault();
            let $input = $(this).parents('.input-number-group').find('.qty');
            let val = parseInt($input.val(), 10);
            if (val > 1) {
                $input.val(val - 1);

                let update_cart = $('button[name="update_cart"]');
                if (update_cart.length > 0) {
                    update_cart.prop('disabled', false)
                }
            }
        });
    }

    // run
    _qty_controls();

    /** ajaxComplete */
    $( document ).ajaxComplete(function( event, xhr, settings ) {
        _qty_controls();
        $('.button.continue').on("click", function (e) {
            e.preventDefault();
            $('.site-header-cart').removeClass('hover');
        });
        $('a.button.add_to_cart_button.ajax_add_to_cart.add-to-cart.product_button').removeClass('active');
    });

    /** */
    const onload_events = () => {

        // let video_list_js_inner = $('.video-list-js-inner');
        // let first_height = video_list_js_inner.find('.first>.item>a').innerHeight();
        // let second = video_list_js_inner.find('.second>.second-inner');
        //
        // second.css({ 'height': first_height - video_list_js_inner.find('.second>.heading-outer').innerHeight() });

        //...
        let grid_products = $('.grid-products');
        grid_products.each((index, el) => {
            let product_button = $(el).find('a.product_button');
            let _position_top = product_button.closest('article.item').innerWidth();
            product_button.css({'top': _position_top - 40});
        });
    }

    onload_events();
    $(window).on('load', () => { onload_events(); });
    device.onChangeOrientation(() => { onload_events(); });
});

/** DOMContentLoaded */
document.addEventListener( 'DOMContentLoaded', () => {

    /*attribute target="_blank" is not W3C compliant*/
    const _blanks = [...document.querySelectorAll('a._blank, a.blank, a[target="_blank"]')];
    _blanks.forEach((el, index) => {
        el.removeAttribute('target');
        el.setAttribute('target', '_blank');
        if (!1 === el.hasAttribute('rel')) {
            el.setAttribute('rel', 'noopener nofollow');
        }
    });

    /** javascript disable right click */
    //document.addEventListener('contextmenu', event => event.preventDefault());

    // document.addEventListener("contextmenu", function(e){
    //     if (e.target.nodeName === "IMG") {
    //         e.preventDefault();
    //     }
    // }, false);

    /** remove style img tag*/
    const _img = document.querySelectorAll('img');
    Array.prototype.forEach.call(_img, (el) => {
        el.removeAttribute('style');
    });
});

/** vars */
const getParameters = (URL) => JSON.parse('{"' + decodeURI(URL.split("?")[1]).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g, '":"') + '"}');
const touchSupported = () => { ('ontouchstart' in window || window.DocumentTouch && document instanceof window.DocumentTouch); };

/**
 * @param cname
 * @returns {unknown}
 */
const getCookie = (cname) => (
    document.cookie.match('(^|;)\\s*' + cname + '\\s*=\\s*([^;]+)')?.pop() || ''
)

/**
 * @param cname
 * @param cvalue
 * @param exdays
 */
function setCookie(cname, cvalue, exdays) {
    let d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

/**
 * @param url
 * @param $delay
 */
function redirect(url = null, $delay = 10) {
    setTimeout(function () {
        if (url === null || url === '' || typeof url === "undefined") {
            document.location.assign(window.location.href);
        } else {
            url = url.replace(/\s+/g, '');
            document.location.assign(url);
        }
    }, $delay);
}

/**
 * @param page
 * @param title
 * @param url
 */
function pushState(page, title, url) {
    if ("undefined" !== typeof history.pushState) {
        history.pushState({page: page}, title, url);
    } else {
        window.location.assign(url);
    }
}

/**
 * @return {number}
 */
function offsetTop() {
    let supportPageOffset = window.pageYOffset !== undefined;
    let isCSS1Compat = ((document.compatMode || "") === "CSS1Compat");
    return supportPageOffset ? window.pageYOffset : isCSS1Compat ? document.documentElement.scrollTop : document.body.scrollTop;
}

/** import Swiper bundle with all modules installed */
import { Swiper } from 'swiper/bundle';
import jquery from 'jquery';

/** product images slides */
const spg_swiper = [...document.querySelectorAll('.swiper-product-gallery')];
spg_swiper.forEach((el, index) => {
    const _rand = nanoid(8),
        _class = 'swiper-product-gallery-' + _rand;

    el.classList.add(_class);
    let w_images = el.querySelector('.swiper-images');
    let w_thumbs = el.querySelector('.swiper-thumbs');

    let swiper_images = false;
    let swiper_thumbs = false;

    /** wpg thumbs */
    if (w_thumbs) {
        w_thumbs.querySelector('.swiper-button-prev').classList.add('prev-thumbs-' + _rand);
        w_thumbs.querySelector('.swiper-button-next').classList.add('next-thumbs-' + _rand);
        w_thumbs.classList.add('thumbs-' + _rand);

        let thumbs_options = {
            spaceBetween: 5,
            slidesPerView: 4,
            watchSlidesProgress: !0,
            mousewheelControl: !0,
            breakpoints: {
                640: {
                    spaceBetween: 10,
                    slidesPerView: 5,
                },
                1024: {
                    spaceBetween: 10,
                    slidesPerView: 6,
                },
            },
            navigation: {
                prevEl: '.prev-thumbs-' + _rand,
                nextEl: '.next-thumbs-' + _rand,
            },
        }

        //if ( is_mobile() || is_tablet() ) {
        //thumbs_options.cssMode = !0;
        //}

        swiper_thumbs = new Swiper('.thumbs-' + _rand, thumbs_options);
    }

    /** wpg images */
    if (w_images) {
        w_images.querySelector('.swiper-button-prev').classList.add('prev-images-' + _rand);
        w_images.querySelector('.swiper-button-next').classList.add('next-images-' + _rand);
        w_images.classList.add('images-' + _rand);

        let images_options = {
            //effect: "fade",
            //speed: 50, // default : 300
            slidesPerView: 'auto',
            spaceBetween: 10,
            watchSlidesProgress: !0,
            breakpoints: {
                640: {
                    spaceBetween: 15,
                }
            },
            navigation: {
                prevEl: '.prev-images-' + _rand,
                nextEl: '.next-images-' + _rand,
            },
        };

        if (swiper_thumbs) {
            images_options.thumbs = {
                swiper: swiper_thumbs,
            }
        }

        //if ( is_mobile() || is_tablet() ) {
        //images_options.cssMode = !0;
        //}

        swiper_images = new Swiper('.images-' + _rand, images_options);

        /** */
        Fancybox.bind('[data-rel="lightbox"]', {
            groupAll: true,
            on: {
                load: (fancybox, slide) => {
                    //console.log(`#${slide.index} slide is loaded!`);

                    // Update position of the slider
                    swiper_images.slideTo(slide.index - 1);
                },
            },
        });

        /** variation image */
        let firstImage = w_images.querySelector('.swiper-images-first img');
        firstImage.removeAttribute('srcset');

        let firstImageSrc = firstImage.getAttribute('src');
        let imagePopupSrc = w_images.querySelector('.swiper-images-first .image-popup');

        /** */
        let firstThumb = false;
        let firstThumbSrc = false;
        let dataLargeImage = false;

        if (swiper_thumbs) {
            firstThumb = w_thumbs.querySelector('.swiper-thumbs-first img');
            firstThumb.removeAttribute('srcset');

            firstThumbSrc = firstThumb.getAttribute('src');
            dataLargeImage = firstThumb.getAttribute('data-large_image');
        }

        /** */
        const variations_form = $('form.variations_form');
        variations_form.on('found_variation', function( event, variation ) {
            if( variation.image.src ) {
                firstImage.setAttribute('src', variation.image.src);
                imagePopupSrc.setAttribute('data-src', variation.image.full_src);
                if (swiper_thumbs) {
                    firstThumb.setAttribute('src', variation.image.gallery_thumbnail_src);
                }

                swiper_images.slideTo(0);
            }
        });

        variations_form.on( 'reset_image', function() {
            firstImage.setAttribute('src', firstImageSrc);
            imagePopupSrc.setAttribute('data-src', dataLargeImage);
            if (swiper_thumbs) {
                firstThumb.setAttribute('src', firstThumbSrc);
            }

            swiper_images.slideTo(0);
        });

        /** */
        //const reset_variations = $( '.reset_variations' );
        //reset_variations.on( 'click', function() {});
    }
});

/** swiper container */
const w_swiper = [...document.querySelectorAll('.w-swiper')];
w_swiper.forEach((el, index) => {
    const _rand = nanoid(12),
        _class = 'swiper-' + _rand,
        _next_class = 'next-' + _rand,
        _prev_class = 'prev-' + _rand,
        _pagination_class = 'pagination-' + _rand,
        _scrollbar_class = 'scrollbar-' + _rand;

    el.classList.add(_class);

    /** swiper controls */
    let _controls = el.closest('.swiper-section').querySelector('.swiper-controls');
    if (_controls == null) {
        _controls = document.createElement("div");
        _controls.classList.add('swiper-controls');
        el.after(_controls);
    }

    /** swiper options */
    const el_swiper_wrapper = el.querySelector('.swiper-wrapper');
    let _obj_options = JSON.parse(el_swiper_wrapper.dataset.options);

    if ( isEmpty(_obj_options) ) {
        _obj_options = {
            autoview: !0,
            loop: !0,
            autoplay: !0,
            navigation: !0,
        };
    }

    /** init options*/
    let _result_options = {};

    //_result_options.lazy = !0;
    _result_options.grabCursor = !0;
    _result_options.allowTouchMove = !0;
    //_result_options.threshold = 0.8;
    _result_options.hashNavigation = !1;
    _result_options.mousewheel = !1;
    //_result_options.observer = !0;

    /** responsive view*/
    let _desktop_data = 0,
        _tablet_data = 0,
        _mobile_data = 0;

    if ( _obj_options.hasOwnProperty('desktop') ) {
        _desktop_data = _obj_options.desktop;
    }

    if (_obj_options.hasOwnProperty('tablet') ) {
        _tablet_data = _obj_options.tablet;
    }

    if ( _obj_options.hasOwnProperty('mobile') ) {
        _mobile_data = _obj_options.mobile;
    }

    if ( !_desktop_data || !_tablet_data || !_mobile_data ) {
        _result_options.autoview = !0;
    }

    /** gap */
    _result_options.spaceBetween = 0;
    if ("gap" in _obj_options) {
        _result_options.spaceBetween = 20;
    } else if ("smallgap" in _obj_options) {
        _result_options.spaceBetween = parseInt(_obj_options.smallgap);
    }

    /** autoview */
    if ("autoview" in _obj_options) {
        _result_options.slidesPerView = 'auto';
        _result_options.loopedSlides = 12;
        if ("gap" in _obj_options) {
            _result_options.breakpoints = {
                640: { spaceBetween: 30 }
            };
        } else if ("smallgap" in _obj_options) {
            _result_options.breakpoints = {
                640: { spaceBetween: parseInt(_obj_options.smallgap) }
            };
        }
    } else {
        _result_options.slidesPerView = parseInt(_mobile_data);
        if ("gap" in _obj_options) {
            _result_options.breakpoints = {
                640: {
                    spaceBetween: 30,
                    slidesPerView: parseInt(_tablet_data)
                },
                1024: {
                    spaceBetween: 30,
                    slidesPerView: parseInt(_desktop_data)
                },
            };
        } else if ("smallgap" in _obj_options) {
            _result_options.breakpoints = {
                640: {
                    spaceBetween: parseInt(_obj_options.smallgap),
                    slidesPerView: parseInt(_tablet_data)
                },
                1024: {
                    spaceBetween: parseInt(_obj_options.smallgap),
                    slidesPerView: parseInt(_desktop_data)
                },
            };
        } else {
            _result_options.breakpoints = {
                640: { slidesPerView: parseInt(_tablet_data) },
                1024: { slidesPerView: parseInt(_desktop_data) },
            };
        }
    }

    if ("autoview" in _obj_options || _result_options.slidesPerView > 1) {
        _result_options.watchSlidesVisibility = !0;
    }

    /** centered*/
    if ("centered" in _obj_options) {
        _result_options.centeredSlides = !0;
    }

    /** speed*/
    if ("speed" in _obj_options) {
        _result_options.speed = parseInt(_obj_options.speed);
    } else {
        _result_options.speed = random(600, 1200);
    }

    /** observer*/
    if ("observer" in _obj_options) {
        _result_options.observer = !0;
        _result_options.observeParents = !0;
    }

    /** group*/
    if ("group" in _obj_options && !("autoview" in _obj_options)) {
        _result_options.slidesPerGroupSkip = !0;
        _result_options.loopFillGroupWithBlank = !0;
        _result_options.slidesPerGroup = parseInt(_obj_options.group);
    }

    /** fade*/
    if ("fade" in _obj_options) {
        _result_options.effect = 'fade';
        _result_options.fadeEffect = { crossFade: !0 };
    }

    /** autoheight*/
    if ("autoheight" in _obj_options) {
        _result_options.autoHeight = !0;
    }

    /** freemode*/
    if ("freemode" in _obj_options) {
        _result_options.freeMode = !0;
    }

    /** loop*/
    if ("loop" in _obj_options && !("row" in _obj_options)) {
        _result_options.loop = !0;
        _result_options.loopFillGroupWithBlank = !0;
    }

    /** autoplay*/
    if ("autoplay" in _obj_options) {
        if ("delay" in _obj_options) {
            _result_options.autoplay = {
                disableOnInteraction: !1,
                delay: parseInt(_obj_options.delay),
            };
        } else {
            _result_options.autoplay = {
                disableOnInteraction: !1,
                delay: random(5500, 6500),
            };
        }
        if ("reverse" in _obj_options) {
            _result_options.reverseDirection = !0;
        }
    }

    /** row*/
    if ("row" in _obj_options) {
        _result_options.direction = 'horizontal';
        _result_options.loop = !1;
        _result_options.grid = {
            rows: parseInt(_obj_options.row),
            fill: 'row',
        };
    }

    /** navigation */
    if ("navigation" in _obj_options) {
        const _section = el.closest('.swiper-section');
        let _btn_prev = _section.querySelector('.swiper-button-prev');
        let _btn_next = _section.querySelector('.swiper-button-next');

        if (_btn_prev && _btn_next) {
            _btn_prev.classList.add(_prev_class);
            _btn_next.classList.add(_next_class);
        } else {
            _btn_prev = document.createElement("div");
            _btn_next = document.createElement("div");

            _btn_prev.classList.add('swiper-button', 'swiper-button-prev', _prev_class);
            _btn_next.classList.add('swiper-button', 'swiper-button-next', _next_class);

            _controls.appendChild(_btn_prev);
            _controls.appendChild(_btn_next);

            _btn_prev.setAttribute("data-glyph", "");
            _btn_next.setAttribute("data-glyph", "");
        }

        _result_options.navigation = {
            nextEl: '.' + _next_class,
            prevEl: '.' + _prev_class,
        };
    }

    /** pagination */
    if ("pagination" in _obj_options) {
        const _section = el.closest('.swiper-section');
        let _pagination = _section.querySelector('.swiper-pagination');
        if (_pagination) {
            _pagination.classList.add(_pagination_class);
        } else {
            let _pagination = document.createElement("div");
            _pagination.classList.add('swiper-pagination', _pagination_class);
            _controls.appendChild(_pagination);
        }

        if (_obj_options.pagination === 'fraction') {
            _result_options.pagination = {
                el: '.' + _pagination_class,
                type: 'fraction',
            };
        } else if (_obj_options.pagination === 'progressbar') {
            _result_options.pagination = {
                el: '.' + _pagination_class,
                type: "progressbar",
            };
        } else if (_obj_options.pagination === 'dynamic') {
            _result_options.pagination = {
                dynamicBullets: !0,
                el: '.' + _pagination_class,
            };
        } else { // custom
            let _pagination = _section.querySelector('.swiper-pagination');
            _pagination.classList.add('swiper-pagination-custom');
            _result_options.pagination = {
                //dynamicBullets: !1,
                el: '.' + _pagination_class,
                renderBullet: function (index, className) {
                    return '<span class="' + className + '">' + (index + 1) + "</span>";
                },
            };
        }

        _result_options.pagination.clickable = !0;
    }

    /** scrollbar */
    if ("scrollbar" in _obj_options) {
        let _swiper_scrollbar = document.createElement("div");
        _swiper_scrollbar.classList.add('swiper-scrollbar', _scrollbar_class);
        _controls.appendChild(_swiper_scrollbar);
        _result_options.scrollbar = {
            hide: !0,
            el: '.' + _scrollbar_class,
        };
    }

    /** vertical*/
    if ("vertical" in _obj_options) {
        _result_options.direction = 'vertical';
    }

    /**parallax*/
    if ("parallax" in _obj_options) {
        _result_options.parallax = !0;
    }

    /**_marquee**/
    if ("marquee" in _obj_options) {
        _result_options.centeredSlides = !0;
        _result_options.autoplay = {
            delay: 1,
            disableOnInteraction: !1
        };

        _result_options.loop = !0;
        _result_options.allowTouchMove = !0;
    }

    /**progress*/
    if ("progressbar" in _obj_options) {
        let _swiper_progress = document.createElement("div");
        _swiper_progress.classList.add('swiper-progress');
        _result_options.appendChild(_swiper_progress);
    }

    /**cssMode*/
    if (!("row" in _obj_options)
        && !("marquee" in _obj_options)
        && !("centered" in _obj_options)
        && !("freemode" in _obj_options)
        && !("progressbar" in _obj_options)
        && ( is_mobile() || is_tablet() )
        && !el.classList.contains('sync-swiper')) {
        //_result_options.cssMode = !0;
    }

    /** progress dom*/
    let _swiper_progress = _controls.querySelector('.swiper-progress');

    /** init*/
    _result_options.on = {
        init: function () {
            let t = this;
            if ("parallax" in _obj_options) {
                t.autoplay.stop();
                t.touchEventsData.formElements = "*";
                const parallax = el.querySelectorAll('.--bg');
                [].slice.call(parallax).map((elem) => {
                    let p = elem.dataset.swiperParallax.replace("%", "");
                    if (!p) {
                        p = 95;
                    }
                    elem.dataset.swiperParallax = toString(p / 100 * t.width);
                });
            }

            if ("progressbar" in _obj_options) {
                _swiper_progress.classList.add('progress');
            }
        },

        slideChange: function () {
            if ("progressbar" in _obj_options) {
                _swiper_progress.classList.remove('progress');
            }

            /** sync*/
            let t = this;
            if (el.classList.contains('sync-swiper')) {
                const el_closest = el.closest('section.section');
                const sync_swipers = Array.from(el_closest.querySelectorAll('.sync-swiper:not(.sync-exclude)'));
                sync_swipers.forEach((item, i) => {
                    let _local_swiper = item.swiper;
                    if ("loop" in _obj_options) {
                        _local_swiper.slideToLoop(t.realIndex, parseInt(_obj_options.speed), true);
                    } else {
                        _local_swiper.slideTo(t.activeIndex, parseInt(_obj_options.speed), true);
                    }
                });
            }
        },

        slideChangeTransitionEnd: function () {
            if ("progressbar" in _obj_options) {
                _swiper_progress.classList.add('progress');
            }
        }
    };

    /**console.log(_obj_options);*/
    let _swiper = new Swiper('.' + _class, _result_options);

    if (!("autoplay" in _obj_options) && !("marquee" in _obj_options)) {
        _swiper.autoplay.stop();
    }

    /** now add mouseover and mouseout events to pause and resume the autoplay;*/
    el.addEventListener('mouseover', () => {
        _swiper.autoplay.stop();
    });

    el.addEventListener('mouseout', () => {
        if ("autoplay" in _obj_options) {
            _swiper.autoplay.start();
        }
    });
});

/** custom swiper */
let thumbs_swiper = new Swiper(".thumbs-swiper", {
    loop: true,
    spaceBetween: 2,
    slidesPerView: 2,
    watchSlidesProgress: true,
    autoplay: {
        delay: 2500,
        disableOnInteraction: false,
    },
    pagination: {
        clickable: true,
        dynamicBullets: !0,
        el: '.two-pagination',
    },
    navigation: {
        nextEl: ".two-button-next",
        prevEl: ".two-button-prev",
    },
    breakpoints: {
        640: {
            slidesPerView: 3,
        },
        1024: {
            slidesPerView: 5,
        },
    },
    on: {
        slideChange: function () {
            let t = this;
            const sync_swiper = document.querySelector('.main-swiper');
            let _local_swiper = sync_swiper.swiper;
            _local_swiper.slideToLoop(t.realIndex, 1200, true);
        }
    }
});

let main_swiper = new Swiper(".main-swiper", {
    loop: true,
    spaceBetween: 2,
    //effect: "fade",
    thumbs: {
        swiper: thumbs_swiper,
    },
});
jQuery(document).ready(function () {
    if (jQuery('body').hasClass('single-product')) { 
        var modelCurr = jQuery('.custom-model .curr').text();
        jQuery('.tabs-heading-wrapper .before .title').text(modelCurr);
        jQuery(window).scroll(function () {
            jQuery('.tabs-content .woocommerce-tabs-panel').each(function () {
                var top = jQuery(window).scrollTop();
                var offset = jQuery(this).offset().top;
                var height = jQuery(this).outerHeight();
                var id = jQuery(this).attr('id');
                // console.log(id);
                if (top + 170 >= offset && top < offset + height) {
                    jQuery('.tabs-heading .tab').removeClass('active');
                    jQuery('.tabs-heading .tab[aria-controls="' + id + '"]').addClass('active');
                    jQuery('#'+id).addClass('active');
                }
            });
            var tabs = jQuery('.tabs-heading-wrapper');
            var scrollTop = jQuery(window).scrollTop();
            // console.log('ScrollTop: ' + scrollTop);
            var elementOffset = jQuery(tabs).offset().top;
            // console.log('offSet: ' + elementOffset);
            var distance = elementOffset - scrollTop;
            // console.log('distance: ' + distance);
            if (distance < jQuery(window).height() && distance > 0 & distance <= 100 ) {
                tabs.addClass('is-stuck');
            } else {
                tabs.removeClass('is-stuck');
            }
        });
    } 
    jQuery('.tabs-heading .tab a').click(function(e) {
        e.preventDefault();
        var target = jQuery(this).attr('href');
        var targetElement = jQuery(target);
        if (targetElement.length) {
            var offsetTop = targetElement.offset().top - 160;
            jQuery('html, body').animate({
                scrollTop: offsetTop
            }, 600);
        }
    });
    jQuery('.custom-model .item').mouseover(function () {
        var textItem = jQuery(this).text();
        jQuery(this).closest('.custom-model').find('.model-wrapper span.text').text(' '+textItem);
    });
    jQuery('a.button.add_to_cart_button.ajax_add_to_cart.add-to-cart.product_button').click(function () {
        jQuery(this).addClass('active');
        setTimeout(function () {
            jQuery(this).removeClass('active');
            jQuery('.site-header-cart').addClass('hover');
        }, 2000);
    });
    jQuery('#tab-title-reviews').remove();
    jQuery('.tabs-heading-wrapper .tabs-heading').each(function() {
        var liCount = jQuery(this).find('li').length;
        if (liCount >= 1) {
            jQuery(this).closest('.tabs-heading-wrapper').addClass('bar');
        } else {
            jQuery(this).closest('.tabs-heading-wrapper').addClass('no-bar');
        }
    });
    // Kiem tra o input phone co gia tri
    jQuery('.woocommerce #review_form #respond input[type="submit"]').prop('disabled', true);
    jQuery('.woocommerce #review_form #respond #tel').on('input', function () {
        var inputVal = jQuery(this).val().trim();
        if (inputVal === '') {
            jQuery('.woocommerce #review_form #respond input[type="submit"]').prop('disabled', true);
        } else {
            // Optionally, check for valid phone number format
            var phonePattern = /^[0-9]{10}$/; // Change this pattern as needed
            if (phonePattern.test(inputVal)) {
                jQuery('.woocommerce #review_form #respond input[type="submit"]').prop('disabled', false);
            } else {
                jQuery('.woocommerce #review_form #respond input[type="submit"]').prop('disabled', true);
            }
        }
    });
    jQuery('.woocommerce #review_form #respond .form-submit #submit').submit(function(event) {
        var inputVal = jQuery('.woocommerce #review_form #respond #tel').val().trim();
        if (inputVal === '') {
            event.preventDefault();
        } else {
            var phonePattern = /^[0-9]{10}$/;
            if (!phonePattern.test(inputVal)) {
                event.preventDefault();
            }
        }
    });
    // Hover gallery thumb product
    jQuery('.woocommerce-product-gallery__wrapper.wpg__thumbs .wpg__thumb').hover(function () {
        var dataThumb = jQuery(this).attr('data-thumb');
        // jQuery(this).find('.swiper-slide-thumb-active').removeClass('swiper-slide-thumb-active');
        jQuery(this).closest('.woocommerce-product-gallery__wrapper.wpg__thumbs').find('.thumb-active').removeClass('thumb-active');
        jQuery(this).parent().addClass('thumb-active');
        jQuery('.woocommerce-product-gallery__wrapper.wpg__images .swiper-slide-active .wpg__image').attr('data-thumb', dataThumb);
        jQuery('.woocommerce-product-gallery__wrapper.wpg__images .swiper-slide-active .wpg__image a.res').attr('href', dataThumb);
        jQuery('.woocommerce-product-gallery__wrapper.wpg__images .swiper-slide-active .wpg__image a.res img').attr('src', dataThumb);
        jQuery('.woocommerce-product-gallery__wrapper.wpg__images .swiper-slide-active .wpg__image a.res img').attr('data-src', dataThumb);
        jQuery('.woocommerce-product-gallery__wrapper.wpg__images .swiper-slide-active .wpg__image a.res img').attr('data-large_image', dataThumb);
        jQuery('.woocommerce-product-gallery__wrapper.wpg__images .swiper-slide-active .wpg__image a.res img').attr('srcset', dataThumb);
        jQuery('.woocommerce-product-gallery__wrapper.wpg__images .swiper-slide-active .wpg__image .image-popup').attr('data-src', dataThumb);
    });
    // Dem slide gallery thumb
    var lengthThumb = jQuery('.woocommerce-product-gallery__wrapper.wpg__thumbs .swiper-slide').length;
    var remainingSlides = lengthThumb - 3;
    jQuery('#remaining-count').text('+'+remainingSlides);
    jQuery('.woocommerce-product-gallery__wrapper.wpg__thumbs .swiper-slide').each(function(index) {
        if (index >= 3) {
            jQuery(this).hide();
        }
    });
    var nextHiddenSlide = jQuery('.woocommerce-product-gallery__wrapper.wpg__thumbs .swiper-slide').eq(3);
    var dataNext = jQuery(nextHiddenSlide).find('.wpg__thumb').attr('data-thumb');
    // console.log(dataNext);
    jQuery('#remaining-slide .wpg__thumb').attr('data-thumb', dataNext);
    jQuery('#remaining-slide .wpg__thumb a').attr('href', dataNext);
    jQuery('#remaining-slide .image-popup').attr('data-src', dataNext);
    // jQuery('#remaining-slide .wpg__thumb').click(function () {
    //     jQuery('.woocommerce-product-gallery__wrapper.wpg__images .swiper-slide-active .wpg__image').click(function () {
    //         alert('quan');
    //     });
    // });
});
jQuery(document).ready(function($) {
    jQuery('.header-inner-cell .inside-search input[type="search"]').on('input', function() {
        var searchQuery = jQuery(this).val();
        // console.log(searchQuery);
        if (searchQuery.length >= 2) {
            jQuery.ajax({
                url: HD.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'woocommerce_ajax_search',
                    query: searchQuery
                },
                success: function (response) {
                    // console.log(response);
                    var suggestions = JSON.parse(response);
                    var suggestionList = '';
                    if (suggestions.length > 0) {
                        suggestions.forEach(function(suggestion) {
                            suggestionList += '<div class="item">' +
                                '<div class="thumbs"><a href="' + suggestion.permalink + '"><img src="' + suggestion.thumbnail + '"></a></div>' +
                                '<div class="title"><a href="' + suggestion.permalink + '">' + suggestion.title + '</a>';
                                if (suggestion.type === 'product') {
                                    var priceHtml = '';
                                    if (suggestion.sale_price !== 'Liên hệ') {
                                        suggestionList += '<p class="price">';
                                        suggestionList += '<span>' + suggestion.sale_price + '</span>';
                                        suggestionList += '<del>' + suggestion.regular_price + '</del>';
                                        suggestionList += '</p>';
                                    } else {
                                        suggestionList += '<p class="price"><span>' + suggestion.price + '</span></p>';
                                    }
                                    suggestionList += priceHtml;
                                }
                                
                            suggestionList += '</div></div>';
                        });
                    } else {
                        suggestionList += '<div class="error">Không tìm thấy kết quả phù hợp!</div>';
                    }
                    jQuery('#ajaxSearchResults').html('<div class="resultsContent">' + suggestionList + '</div>');
                }
            });
        } else {
            jQuery('#suggestions').empty();
        }
    });
    jQuery(document).on('click', function(event) {
        var $target = jQuery(event.target);
        if (!$target.closest('.header-inner-cell .inside-search input[type="search"]').length && !$target.closest('#ajaxSearchResults').length) {
            jQuery('#ajaxSearchResults').hide();
        } else {
            jQuery('#ajaxSearchResults').show();
        }
    });
});