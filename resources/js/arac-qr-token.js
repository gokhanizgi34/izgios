export function tokenFromQr(raw) {
    const text = String(raw || '').trim();
    const uuid = '[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}';
    return (text.match(new RegExp('^(' + uuid + ')$', 'i'))?.[1]
        || text.match(new RegExp('/arac/(' + uuid + ')(?:[/?#]|$)', 'i'))?.[1] || '').toLowerCase();
}
