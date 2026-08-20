console.log("İZGİOS Büyük Harf Motoru Aktif");


document.addEventListener("input", function(e){


    const alan = e.target;


    if(
        alan.tagName !== "INPUT" &&
        alan.tagName !== "TEXTAREA"
    ){
        return;
    }

    if(alan.closest('[data-preserve-case]')){
        return;
    }



    const type = alan.getAttribute("type");


    // Mail ve şifre hariç
    if(
        type === "email" ||
        type === "password"
    ){
        return;
    }



    alan.value = alan.value.toLocaleUpperCase("tr-TR");


});
