function imprimirWindow(objeto) {
    var objetoSeleccionado = document.getElementById(objeto);

    var imprimirObjeto = window.open(" ", "popimpr");
    imprimirObjeto.document.write(objetoSeleccionado.innerHTML);
    imprimirObjeto.document.close();

    imprimirObjeto.print();
    imprimirObjeto.close();
}
