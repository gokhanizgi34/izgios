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


    // Mail, şifre ve büyük/küçük harfe duyarlı bağlantılar hariç
    if(
        type === "email" ||
        type === "password" ||
        type === "url"
    ){
        return;
    }



    alan.value = alan.value.toLocaleUpperCase("tr-TR");


});
