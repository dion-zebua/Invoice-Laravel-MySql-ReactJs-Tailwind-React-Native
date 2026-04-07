const telephone = () => {
    return '+62 882-8931-7870'
}

const whatsapp = (message = null) => {
    return `https://api.whatsapp.com/send/?phone=${telephone().replace(/[^\d]/g, '')}&text=${message ? encodeURIComponent(message) : 'Saya+ingin+daftar+' + process.env.APP_URL_FRONTEND}`
}

const convertDate = (time) => {
    const date = new Date(time.replace(' ', 'T'));

    const yyyy = date.getFullYear();
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    const dd = String(date.getDate()).padStart(2, '0');

    return `${yyyy}-${mm}-${dd}`;

}

const convertToRupiah = (number) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
        currency: "IDR",
    }).format(number ?? 0).replace(/\s/g, "")
}
const helper = { telephone, whatsapp, convertDate, convertToRupiah }
export default helper