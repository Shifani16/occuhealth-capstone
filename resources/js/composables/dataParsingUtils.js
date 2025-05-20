// src/utils/dataParsingUtils.js

/**
 * Checks if a value is a valid number.
 * @param {*} value
 * @returns {boolean}
 */
export function isValidNumber(value) {
    const num = parseFloat(value);
    return !isNaN(num);
}

/**
 * Parses a TD string (e.g., "120/80") into sistolik and diastolik numbers.
 * @param {string} tdString
 * @returns {{sistolik: number, diastolik: number}} Returns NaN for invalid parts.
 */
export function parseTD(tdString) {
    if (!tdString || typeof tdString !== 'string' || !tdString.includes('/')) {
        return { sistolik: NaN, diastolik: NaN };
    }
    const parts = tdString.split('/');
    const sistolik = parseFloat(parts[0]);
    const diastolik = parseFloat(parts[1]);
    return { sistolik, diastolik };
}

/**
 * Parses the Kolesterol Total value from a string like "233 Borderline".
 * Assumes the number is at the beginning.
 * @param {string} kolesterolString
 * @returns {number} Returns NaN if parsing fails.
 */
export function parseKolesterolTotal(kolesterolString) {
    if (!kolesterolString || typeof kolesterolString !== 'string') {
        return NaN;
    }
    // Use regex to find the first number in the string
    const match = kolesterolString.match(/^\s*(\d+(\.\d+)?)/);
    if (match && match[1]) {
        return parseFloat(match[1]);
    }
    return NaN;
}

/**
 * Finds predefined keywords in a riwayat string (case-insensitive).
 * (Currently implemented directly in calculateRiwayatKesehatanRekap for clarity,
 * but could be moved here if logic becomes complex)
 * @param {string} riwayatString
 * @param {Object} keywordsMap - Map of lowercased keyword -> canonical name
 * @returns {Set<string>} Set of canonical names found
 */
export function findRiwayatKeywords(riwayatString, keywordsMap) { /* ... logic ... */ }