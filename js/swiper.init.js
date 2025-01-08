if ($(".our-brands-swiper").length > 0) {
  const swiper = new Swiper(".our-brands-swiper", {
      loop: true,
      slidesPerView: 2,
      centeredSlides: true,
      spaceBetween: 48,
      breakpoints: {
          700: {
              slidesPerView: 3
          },
          1000: {
              slidesPerView: 4
          },
          1300: {
              slidesPerView: 5
          },
      },
      navigation: {
          nextEl: ".our-brands-swiper-next",
          prevEl: ".our-brands-swiper-prev",
      },
  });
}