document.addEventListener('DOMContentLoaded', function () {
    const villeSelect = document.querySelector('#sortie_Ville');
    const lieuSelect = document.querySelector('#sortie_lieu');
    const codePostalInput = document.querySelector('#sortie_Code_Postal');
    const rueInput = document.querySelector('#sortie_rue');
    const latInput = document.querySelector('#sortie_latitude');
    const longInput = document.querySelector('#sortie_longitude');

    villeSelect.addEventListener('change', function () {
        const selectedVilleId = this.value;

        fetch(`get-lieux-by-ville?villeId=${selectedVilleId}`)
            .then(response => response.json())
            .then(data =>{
                lieuSelect.innerHTML= '';

                const placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = 'Choisissez un lieu';
                lieuSelect.appendChild(placeholderOption);

                for (const lieuId in data){
                    const option = document.createElement('option');
                    option.value = lieuId;
                    option.textContent= data[lieuId];
                    lieuSelect.appendChild(option);
                }
            })
            .catch(error => console.error('Error', error));
    });

    villeSelect.addEventListener('change', function (){
        const selectedvilleId = this.value;

        fetch(`get-cp-by-ville?villeId=${selectedvilleId}`)
            .then(response=>response.json())
            .then(data =>{
                codePostalInput.value = data.codePostal;
            })
    });

    lieuSelect.addEventListener('change', function () {
        const  selectedLieuId = this.value;

        fetch(`get-lieu-params?lieuId=${selectedLieuId}`)
            .then(response=>response.json())
            .then(data=>{
                rueInput.value = data.rue;
                latInput.value = data.lat;
                longInput.value = data.long;
            })
    });

});



