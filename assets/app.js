// A ordem importa: jQuery, Popper.js e depois Bootstrap
import $ from 'jquery';
import 'popper.js';
import 'bootstrap';
window.$ = $;
window.jQuery = $;

// Importe os arquivos CSS do tema
import './vendor/fontawesome-free/css/all.min.css';
import './css/sb-admin-2.min.css';
import './vendor/datatables/dataTables.bootstrap4.min.css';

// Importe o seu arquivo de estilo personalizado por último
import './css/custom.css';

// Importe os scripts do tema
import './js/sb-admin-2.min.js';
