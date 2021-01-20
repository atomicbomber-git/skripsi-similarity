require('select2')
const Swal = require('sweetalert2')
window.Popper = require('popper.js').default
window.$ = window.jQuery = require('jquery')
require("bootstrap")
require("alpinejs")

require('./bootstrap');

window.confirmDialog = (attributes) => {
    return Swal.fire({
        title: `Konfirmasi`,
        titleText: `Konfirmasi Tindakan`,
        text: `Apakah Anda yakin ingin melakukan tindakan ini?`,
        icon: `warning`,
        showCancelButton: true,
        confirmButtonText: `Ya`,
        cancelButtonText: `Tidak`,
        ...attributes,
    })
}
