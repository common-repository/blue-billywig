if ( document.querySelector('input[name="bb-api-id"]') ) {
    let apiIdMask = IMask( document.querySelector('input[name="bb-api-id"]'), {
      mask: Number
    });
}
if ( document.querySelector('input[name="bb-publication"]') ) {
    let apiPublicationMask = IMask( document.querySelector('input[name="bb-publication"]'), {
        mask: /^([a-zA-Z0-9!@#$%^*_|:/.])+$/
    });
}

if ( document.querySelector('input[name="bb-api-secret"]') ) {
    let apiSecretMask = IMask( document.querySelector('input[name="bb-api-secret"]'), {
        mask: /^([a-zA-Z0-9!@#$%^*_|:/.])+$/
    });
} 