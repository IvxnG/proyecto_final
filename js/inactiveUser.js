if(localStorage.getItem("idUsuario")){
    //Comprobar se el usuario dejo la web inactiva para cerrar sesion
    function inactiveUser(){
        // Limpiar el almacenamiento local después de 10 minutos de inactividad
        const inactivityTimeout = 10 * 60 * 1000;
        let timer;
    
        function resetTimer() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                localStorage.clear();
                location.href = "https://easymarketivan.000webhostapp.com/index.html"
            }, inactivityTimeout);
        }
    
        // Reiniciar el temporizador en cada evento de interacción
        document.addEventListener('click', resetTimer);
        document.addEventListener('mousemove', resetTimer);
        document.addEventListener('keypress', resetTimer);
    }
    
    // Llamar a la función para iniciarla
    inactiveUser();
}