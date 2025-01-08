const openDropdownButtonsHomepage = document.querySelectorAll(".open-question-btn-home")
const questionAnswersHome = document.querySelectorAll(".answer-home")

openDropdownButtonsHomepage.forEach((btn, idx) => {
    btn.addEventListener("click", ()=>{
        questionAnswersHome[idx].classList.toggle("open")
    })
})

const menuToggle = document.getElementById("menu-toggle")
const mobileMenu = document.getElementById("mobile-menu")
menuToggle.addEventListener("click", ()=>{
    menuToggle.classList.toggle("menu-toggle-active")
    mobileMenu.classList.toggle("open")
})