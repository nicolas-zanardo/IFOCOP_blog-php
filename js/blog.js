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

                    // pour les articles
                    if(document.getElementById('nom_original')) {
                        // mémoriser les informations du fichier image
                        document.getElementById('nom_original').setAttribute('value', fichier.name);
                        document.getElementById('data_img').setAttribute('value', e.target.result);
                    }
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

    // Drag & Drop
    if(document.getElementById('preview')) {
        document.addEventListener('dragover', (e)=> {
            e.stopPropagation();
            e.preventDefault();
            document.getElementById('preview').style.border = "4px dashed blue";
        })

        document.addEventListener('dragleave', (e)=> {
            e.stopPropagation();
            e.preventDefault();
            document.getElementById('preview').style.border = "";
        })

        document.addEventListener('drop', (e)=> {
            e.stopPropagation();
            e.preventDefault();
            document.getElementById('preview').style.border = "";
        })

        document.getElementById('preview').addEventListener('drop', (e) => {
            document.getElementById('preview').style.border ="";
            let fichier = e.dataTransfer.files;
            // alimenter l'input de type files avec cette information
            document.getElementById('header').files = fichier;

            let event = new Event('change');
            document.getElementById('header').dispatchEvent(event);
        })
    }


}) // *fin du DOM chargé
