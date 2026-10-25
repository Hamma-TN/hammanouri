function valid1() {
    const idt = document.getElementById("idt").value;
    const prix = Number(document.getElementById("pv").value);
    const btn1 = document.getElementById("btn1").checked;
    const btn2 = document.getElementById("btn2").checked;
    const btn3 = document.getElementById("btn3").checked;

    if (idt.length !== 5 || !verif(idt)) {
        alert("identifiant invalid")
        return false
    } else if (!Number.isFinite(prix) || prix < 100) {
        alert("prix de vente invalid ")
        document.getElementById("pv").style.background="red"
        return false
    } else if (!btn1 && !btn2 && !btn3) {
        alert("choisir une composition SVP")
        return false
    }
    return true
}

function verif(ch) {
    return /^[A-Z]{2}\d{3}$/i.test(ch);
}

function valid2() {
    const idt = document.getElementById("idt").value;
    const exp = document.getElementById("exp").selectedIndex;
    const fini = Number(document.getElementById("fin").value);
    const com = document.getElementById("com").value;
    const btn1 = document.getElementById("btn1").checked;
    const btn2 = document.getElementById("btn2").checked;

    if (idt.length !== 5 || !verif(idt)) {
        alert("identifiant invalid")
        return false
    } else if (exp === 0) {
        alert("choisir un expert SVP")
        return false
    } else if (!Number.isFinite(fini) || fini < 1 || fini > 5) {
        alert("finition invalid")
        return false
    } else if (!btn1 && !btn2) {
        alert("choisir un option")
        return false
    } else if (!nbmot(com)) {
        alert("saisir un commaintaire")
        return false
    } else {
        return true
    }
}

function nbmot(ch) {
    const mots = nettoyage(ch).split(" ");
    return mots.length === 2 && mots.every(alpha);
}

function nettoyage(ch) {
    return ch.trim().replace(/\s+/g, " ");
}

function alpha(ch) {
    return /^[A-Z]+$/i.test(ch);
}