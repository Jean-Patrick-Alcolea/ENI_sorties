document.addEventListener('DOMContentLoaded', function () {
    const lieuSelect = document.querySelector('#modif_sortie_lieu');
    const rueInput = document.querySelector('#sortie_rue');
    const latInput = document.querySelector('#sortie_latitude');
    const longInput = document.querySelector('#sortie_longitude');

    lieuSelect.addEventListener('change', function () {
        const  selectedLieuId = this.value;

        fetch(`http://localhost/Sorties/public/sorties/get-lieu-params?lieuId=${selectedLieuId}`)
            .then(response=>response.json())
            .then(data=>{
                rueInput.value = data.rue;
                latInput.value = data.lat;
                longInput.value = data.long;
            })
    });

});



