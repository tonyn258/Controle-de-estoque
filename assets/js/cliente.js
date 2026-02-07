document.addEventListener('DOMContentLoaded', function() {
    // Capitalização Automática do Nome (Ex: joao silva -> Joao Silva)
    var nomeInput = document.getElementById('NomeCliente');
    
    if(nomeInput) {
        nomeInput.addEventListener('input', function(e) {
            var start = this.selectionStart;
            var end = this.selectionEnd;
            this.value = this.value.toLowerCase().replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); });
            this.setSelectionRange(start, end);
        });
    }
});