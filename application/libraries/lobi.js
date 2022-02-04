function mensajeLobibox(tipo,mensaje){
      Lobibox.notify(tipo, {
            msg: mensaje,
            iconSource: "fontAwesome",
            size: 'mini',
            width: 400,
            rounded: true,                
            delay: 5000,
            sound: false,
            position: 'top center',
            delayIndicator: false,
      });   
}