document.addEventListener('DOMContentLoaded', function() {

    if(document.getElementById('preview')) {
        document.getElementById('header').addEventListener('change', function(event){
            let fichier = event.target.files[0];
            let ext = ['image/jpeg', 'image/png'];
            if(ext.includes(fichier.type)) {
                let reader = new FileReader();
                // lecture
                reader.readAsDataURL(fichier);
                // action
                reader.onload = (e) => {
                    document.querySelector('#preview img').setAttribute('src', e.target.result);
                }
            }
        });
    }

    if(document.querySelectorAll('a.confirm')) {

        let confirmations = document.querySelectorAll('a.confirm');
        for(let i=0; i < confirmations.length; i++ ) {
            confirmations[i].onclick = () => {
                return( confirm('Etes-vous sur(e) de vouloir supprimer cet élément ?'))
            }
        }
    }

})
