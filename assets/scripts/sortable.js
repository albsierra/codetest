import Sortable from 'sortablejs';

$(() => {

    let lista = document.getElementById('listaPrueba');
    if(!lista){
        return;
    }
    
    Sortable.create(lista, {
        dataIdAtrr: "exercise-id",
        animation: 150,
        handle: '.drag-handle',
        onStart: function (e) {
            $(this).attr('data-previndex', e.oldIndex);
        },
        onUpdate: function (e) {
            var newIndex = e.newIndex;
            var oldIndex = $(this).attr('data-previndex');
            var exerciseId = e.item.getAttribute('exercise-id');
            updateList(exerciseId, oldIndex, newIndex);
        } 
    });
})


