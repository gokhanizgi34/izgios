console.log("İZGİOS Dashboard JS Yüklendi");


document.addEventListener("DOMContentLoaded", function () {


    const menuButton = document.querySelector("#mobile-menu-btn");
    const sidebar = document.querySelector("#izgios-sidebar");
    const overlay = document.querySelector("#sidebar-overlay");


    if(!menuButton || !sidebar){
        console.log("Menü elemanı bulunamadı");
        return;
    }



    function menuToggle(){


        sidebar.classList.toggle("open");

        if(overlay){
            overlay.classList.toggle("active");
        }


        document.body.classList.toggle("menu-open");


    }





    menuButton.addEventListener("click",function(e){

        e.preventDefault();
        e.stopPropagation();

        menuToggle();

    });





    if(overlay){


        overlay.addEventListener("click",function(){


            sidebar.classList.remove("open");

            overlay.classList.remove("active");

            document.body.classList.remove("menu-open");


        });


    }




});