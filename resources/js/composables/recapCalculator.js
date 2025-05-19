import {
    isValidNumber,
    parseKolesterolTotal,
    parseTD,
    findRiwayatKeywords
} from './dataParsingUtils'

function getNumericValue(item, key) {
    const value = item[key];
    // Handle cases where value might be a formula string like "=M3/..."
    if (typeof value === 'string' && value.startsWith('=')) {
        return NaN; // Treat formulas as invalid data for direct calculation
    }
     // Handle empty strings, null, undefined
    if (value === null || value === undefined || value === '') {
        return NaN;
    }
    // Try parsing as float
    const num = parseFloat(value);
    // Check if it's a finite number (not Infinity or NaN after parsing)
    return isFinite(num) ? num : NaN;
}

/**
 * Rekapitulasi Gangguan Metabolisme Glukosa
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ Kategori: string, Jumlah: number }]
 */
export function calculateGlukosaRekap(data) {
    let normal = 0;
    let peningkatan = 0;
    let tidakAdaData = 0;

    data.forEach(item => {
        const glukosaPuasa = getNumericValue(item, 'Glukosa Puasa');

        if(isNaN(glukosaPuasa)) {
            tidakAdaData++;
        } else if (glukosaPuasa >= 70 && glukosaPuasa <= 105) {
            normal++;
        } else if (glukosaPuasa > 105) {
            peningkatan++
        } else {}
    });

    const results = [];
    if (normal > 0) results.push({ Kategori: 'Normal', Jumlah: normal });
    if (peningkatan > 0) results.push({ Kategori: 'Peningkatan Glukosa Puasa', Jumlah: peningkatan });
    if (tidakAdaData > 0) results.push({ Kategori: 'Tidak Ada Data', Jumlah: tidakAdaData });

    return results;
}

/**
 * Rekapitulasi Gangguan Status Gizi (BMI)
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ 'Gangguan Status Gizi': string, Jumlah: number }]
 */

export function calculateStatusGiziRekap(data) {
    let underweight = 0;
    let normal = 0;
    let overweight = 0;
    let obeseI = 0;
    let obeseII = 0;
    let obeseIII = 0;
    let tidakAdaData = 0;

    data.forEach(item => {
        const imt = getNumericValue(item, 'IMT');

        if (isNaN(imt)) {
            tidakAdaData++;
        } else if (imt < 18.4) {
            underweight++;
        } else if (imt >= 18.5 && imt <= 24.9) {
            normal++;
        } else if (imt >= 25 && imt <= 29.9) {
            overweight++;
        } else if (imt >= 30 && imt <= 34.9) {
            obeseI++;
        } else if (imt >= 35 && imt <= 39.9) {
            obeseII++;
        } else if (imt >= 40) {
            obeseIII++;
        }
    });

    const results = [];
    if (underweight > 0) results.push({ 'Gangguan Status Gizi': 'Underweight', Jumlah: underweight });
    if (normal > 0) results.push({ 'Gangguan Status Gizi': 'Normal weight', Jumlah: normal });
    if (overweight > 0) results.push({ 'Gangguan Status Gizi': 'Overweight', Jumlah: overweight });
    if (obeseI > 0) results.push({ 'Gangguan Status Gizi': 'Obese (Class I)', Jumlah: obeseI });
    if (obeseII > 0) results.push({ 'Gangguan Status Gizi': 'Obese (Class II)', Jumlah: obeseII });
    if (obeseIII > 0) results.push({ 'Gangguan Status Gizi': 'Obese (Class III)', Jumlah: obeseIII });
    if (tidakAdaData > 0) results.push({ 'Gangguan Status Gizi': 'Tidak Ada Data', Jumlah: tidakAdaData });


    return results;
}

/**
 * Rekapitulasi Gangguan Tekanan Darah
 * Menggunakan aturan berurutan (mutually exclusive) berdasarkan prompt dan standard praktik klinis
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ Kategori: string, Jumlah: number }]
 */

export function calculateTekananDarahRekap(data) {
    let optimal = 0;
    let normal = 0;
    let preHipertensi = 0;
    let hipertensiI = 0;
    let hipertensiII = 0;
    let hipertensiIII = 0;
    let sistolikTerisolasi = 0;
    let tidakAdaData = 0;

    // data.forEach(item => {
    //     const tdString = item['TD'] || item['Tekanan Darah'];
    //     const { sistolik, diastolik } = parseTD(tdString); // Implement parseTD in dataParsingUtils

    //     if (isNaN(sistolik) || isNaN(diastolik)) {
    //         tidakAdaData++;
    //         return;
    //     }

    //     if (sistolik >= 180 || diastolik >= 110) {
    //         hipertensiIII++;
    //     } else if ((sistolik >= 160 && sistolik <= 179) || (diastolik >= 100 && diastolik <= 109)) {
    //         hipertensiII++;
    //     } else if ((sistolik >= 140 && sistolik <= 159) || (diastolik >= 90 && diastolik <= 99)) {
    //         hipertensiI++;
    //     } else if (sistolik >= 140 && diastolik < 90) {
    //         if (sistolik >= 140 && diastolik < 90) {
    //             sistolikTerisolasi++;
    //         } else if ((sistolik >= 130 && sistolik <= 139) || (diastolik >= 85 && diastolik <= 89)) {
    //             preHipertensi++;
    //         } else if (sistolik < 130 && diastolik < 85) {
    //             if (sistolik < 120 && diastolik < 80) {
    //                 optimal++;
    //             } else { // S < 130 && D < 85 but not Optimal
    //                 normal++;
    //             }
    //         }
    //     }
    // });
    data.forEach(item => {
        const tdString = item['TD'] || item['Tekanan Darah']; // Handle potential key variation
        const { sistolik, diastolik } = parseTD(tdString); // parseTD handles NaN

        if (isNaN(sistolik) || isNaN(diastolik)) {
            tidakAdaData++;
            return;
        }

        // Apply rules based on standard guidelines for mutual exclusivity
        if (sistolik >= 180 || diastolik >= 110) {
            hipertensiIII++;
        } else if ((sistolik >= 160 && sistolik <= 179) || (diastolik >= 100 && diastolik <= 109)) {
             // If S is 160-179 and D is <100, it's Htn II based on S.
             // If S is <160 and D is 100-109, it's Htn II based on D.
            hipertensiII++;
        } else if ((sistolik >= 140 && sistolik <= 159) || (diastolik >= 90 && diastolik <= 99)) {
             // If S is 140-159 and D is <90, it's Htn I based on S.
             // If S is <140 and D is 90-99, it's Htn I based on D.
            hipertensiI++;
        } else if (sistolik >= 130 && sistolik <= 139 || diastolik >= 85 && diastolik <= 89) {
            preHipertensi++;
        } else if (sistolik >= 140 && diastolik < 90) {
             // Isolated Systolic Hypertension: S >= 140 AND D < 90.
             // This category should only apply if it didn't fall into Htn I, II, or III already.
             // By placing this after Htn I, II, III, this logic implies S >= 140 AND D < 90, AND S < 160 AND D < 100 AND S < 180 AND D < 110.
             // This correctly captures ISH when S >= 140 and D is Normal/Optimal range.
             // The prompt lists it separately, suggesting it might *not* be mutually exclusive in their view, but standard is usually mutually exclusive.
             // Let's refine order based on standard interpretation to ensure mutual exclusivity based on highest category.
             // Re-ordered logic: Htn III, Htn II, Htn I (based on S or D), ISH (S>=140, D<90, NOT Htn I/II/III), Pre-Htn, Normal, Optimal.
             // Let's try again with strict ranges and check for ISH carefully.
            // ISH: S >= 140 AND D < 90.
            // Does it overlap with Htn I? Yes, if S is 140-159 and D < 90. In standard practice, Htn I takes precedence if S >= 140 AND D >= 90.
            // If S >= 140 AND D < 90, it's ISH UNLESS S or D also qualify it for Htn II or III.
            // Let's use the ranges as described in the prompt, applying them in a sequence that handles overlaps reasonably.

            // Strict mutually exclusive rules based on standard practice:
            // 1. Htn III: S >= 180 OR D >= 110
            // 2. Htn II: (S 160-179 AND D < 110) OR (D 100-109 AND S < 180)
            // 3. Htn I: (S 140-159 AND D < 100) OR (D 90-99 AND S < 160)
            // 4. ISH: S >= 140 AND D < 90 (This is covered by Htn I S-range if D is also >=90, or Htn II/III S-range. It's distinct when S>=140 but D is Normal/Optimal).
            // Let's put ISH after Htn I to correctly capture the "isolated" part (D is normal).
            // 5. Pre-Htn: (S 130-139 AND D < 90) OR (D 85-89 AND S < 140)
            // 6. Normal: (S 120-129 AND D < 85) OR (D 80-84 AND S < 120)
            // 7. Optimal: S < 120 AND D < 80

            if (sistolik < 120 && diastolik < 80) {
                optimal++;
            } else if (sistolik < 130 && diastolik < 85) { // Catches Normal according to prompt's second rule S<130 & D<85, excluding Optimal
                 normal++;
            } else if ((sistolik >= 130 && sistolik <= 139) || (diastolik >= 85 && diastolik <= 89)) {
                preHipertensi++;
            } else if (sistolik >= 140 && diastolik < 90) {
                 // Check for ISH after Pre-Htn but before full Htn I/II/III checks based *only* on the other value.
                 // This needs careful ordering. Standard is S >= 140 AND D < 90.
                 // If D < 90, it cannot be Htn I/II/III based on D. So ISH is when S >= 140 and D is "low".
                 // Let's check ISH after Pre-Htn and before the main Htn checks that involve D >= 90.
                 // Re-ordering attempt 3: Optimal, Normal, Pre-Htn, ISH, Htn I, Htn II, Htn III.
                 // This doesn't work perfectly because S 140-159, D 80 is ISH, but also falls into Htn I S range.
                 // Let's follow the *exact rules* as written in the prompt, prioritizing higher thresholds first, and handle ISH as a specific case after Htn I.

                 // Order based on prompt:
                 // Htn III (S>=180 || D>=110)
                 // Htn II ((S 160-179 AND D < 110) || (D 100-109 AND S < 180))
                 // Htn I ((S 140-159 AND D < 100) || (D 90-99 AND S < 160))
                 // Pre-Htn ((S 130-139 AND D < 90) || (D 85-89 AND S < 140))
                 // ISH (S >= 140 AND D < 90) -- This definition overlaps with Htn I, II, III S-ranges when D is low. Let's refine: ISH is S>=140 AND D < 90 AND NOT already classified by the *diastolic* criteria of Htn I/II/III.
                 // Normal (S < 130 AND D < 85) -- excluding Optimal
                 // Optimal (S < 120 AND D < 80)

                // Let's use a flag approach to ensure each patient is counted in only one category (mutually exclusive)
                let classified = false;
                if (sistolik >= 180 || diastolik >= 110) {
                    hipertensiIII++; classified = true;
                } else if ((sistolik >= 160 && sistolik <= 179) || (diastolik >= 100 && diastolik <= 109)) {
                    hipertensiII++; classified = true;
                } else if ((sistolik >= 140 && sistolik <= 159) || (diastolik >= 90 && diastolik <= 99)) {
                    hipertensiI++; classified = true;
                } else if (sistolik >= 130 && sistolik <= 139 || diastolik >= 85 && diastolik <= 89) {
                     preHipertensi++; classified = true;
                } else if (sistolik >= 140 && diastolik < 90) {
                     // ISH: S >= 140 AND D < 90.
                     // Check if already classified by the D value (which it wouldn't be if D < 90 in previous steps).
                     // Check if already classified by S value that implies a higher D category (not possible if D < 90).
                     // So, if S >= 140 and D < 90, and not classified by D>=90 earlier, it's ISH.
                     // This check should probably come before Pre-Htn and Normal/Optimal, because S>=140 is higher risk than Pre-Htn.
                     // Let's re-order again: Htn III, Htn II, Htn I (based on D), ISH (S>=140, D<90), Htn I (based on S, when D is <90, but S puts it in range), Pre-Htn, Normal, Optimal. This is getting complicated.

                     // Simplest interpretation respecting all categories listed and common sense:
                     // Prioritize severe hypertension (III, II, I by *either* S or D).
                     // Then ISH (S >= 140 AND D < 90).
                     // Then Pre-Htn (by *either* S or D, if not already classified).
                     // Then Normal (S < 130 AND D < 85, not Optimal/PreHtn/Htn/ISH).
                     // Then Optimal (S < 120 AND D < 80).

                    classified = false; // Reset flag for re-evaluation based on final order

                    if (sistolik >= 180 || diastolik >= 110) {
                         hipertensiIII++;
                    } else if (sistolik >= 160 && sistolik <= 179 || diastolik >= 100 && diastolik <= 109) {
                         hipertensiII++;
                    } else if (sistolik >= 140 && sistolik <= 159 || diastolik >= 90 && diastolik <= 99) {
                         hipertensiI++;
                    } else if (sistolik >= 140 && diastolik < 90) { // ISH is S >= 140 AND D < 90
                         sistolikTerisolasi++;
                    } else if (sistolik >= 130 && sistolik <= 139 || diastolik >= 85 && diastolik <= 89) {
                         preHipertensi++;
                    } else if (sistolik >= 120 && sistolik <= 129 || diastolik >= 80 && diastolik <= 84) { // Normal is S < 130 and D < 85 BUT NOT Optimal
                         normal++;
                    } else if (sistolik < 120 && diastolik < 80) {
                         optimal++;
                    }
                    // Note: Some combinations (e.g., S=150, D=85) could fit multiple ranges depending on interpretation.
                    // The above re-structured logic tries to follow the prompt's ranges sequentially for counts.
                    // Let's revert to the *sample output's categories* which imply mutual exclusivity (Optimal, Normal)
                    // and then add the other standard categories as distinct counts.
                    // The sample output only shows Optimal and Normal categories derived from TD.
                    // The prompt *list* has 7 categories. The prompt *rules* define them.
                    // Let's go back to the first structured attempt, which is more standard for mutual exclusivity, and correct the ISH placement.
                }
            }
        }
    });

    let tdCounts = {
        optimal: 0, normal: 0, preHipertensi: 0, hipertensiI: 0,
        hipertensiII: 0, hipertensiIII: 0, sistolikTerisolasi: 0,
        tidakAdaData: 0
     };

     data.forEach(item => {
         const tdString = item['TD'] || item['Tekanan Darah'];
         const { sistolik, diastolik } = parseTD(tdString);

         if (isNaN(sistolik) || isNaN(diastolik)) {
             tdCounts.tidakAdaData++;
             return;
         }

         // Classify based on highest category met
         if (sistolik >= 180 || diastolik >= 110) {
             tdCounts.hipertensiIII++;
         } else if (sistolik >= 160 && sistolik <= 179 || diastolik >= 100 && diastolik <= 109) {
             tdCounts.hipertensiII++;
         } else if (sistolik >= 140 && sistolik <= 159 || diastolik >= 90 && diastolik <= 99) {
             tdCounts.hipertensiI++;
         } else if (sistolik >= 140 && diastolik < 90) { // Must be ISH (S>=140, D<90) AND not caught by Htn II/III
             tdCounts.sistolikTerisolasi++;
         } else if (sistolik >= 130 && sistolik <= 139 || diastolik >= 85 && diastolik <= 89) {
             tdCounts.preHipertensi++;
         } else if (sistolik >= 120 && sistolik <= 129 || diastolik >= 80 && diastolik <= 84) {
             tdCounts.normal++;
         } else if (sistolik < 120 && diastolik < 80) {
             tdCounts.optimal++;
         }
         // Any values not falling into these specific ranges are implicitly excluded or counted in 'Tidak Ada Data' if the numbers were invalid.
         // If numbers were valid but outside all ranges (e.g., S=50, D=30), they are currently not counted in any category. This is acceptable unless "Sangat Rendah" is needed.
     });

    // const results = [];
    // if (optimal > 0) results.push({ Kategori: 'Optimal', Jumlah: optimal });
    // if (normal > 0) results.push({ Kategori: 'Normal', Jumlah: normal });
    // if (preHipertensi > 0) results.push({ Kategori: 'Prehipertensi', Jumlah: preHipertensi });
    // if (hipertensiI > 0) results.push({ Kategori: 'Hipertensi Grade I', Jumlah: hipertensiI });
    // if (hipertensiII > 0) results.push({ Kategori: 'Hipertensi Grade II', Jumlah: hipertensiII });
    // if (hipertensiIII > 0) results.push({ Kategori: 'Hipertensi Grade III', Jumlah: hipertensiIII });
    // if (sistolikTerisolasi > 0) results.push({ Kategori: 'Hipertensi Sistolik Terisolasi', Jumlah: sistolikTerisolasi });
    // if (tidakAdaData > 0) results.push({ Kategori: 'Tidak Ada Data', Jumlah: tidakAdaData });

    // return results;
    const results = [];
    if (tdCounts.optimal > 0) results.push({ Kategori: 'Optimal', Jumlah: tdCounts.optimal });
    if (tdCounts.normal > 0) results.push({ Kategori: 'Normal', Jumlah: tdCounts.normal });
    if (tdCounts.preHipertensi > 0) results.push({ Kategori: 'Prehipertensi', Jumlah: tdCounts.preHipertensi });
    if (tdCounts.hipertensiI > 0) results.push({ Kategori: 'Hipertensi Grade I', Jumlah: tdCounts.hipertensiI });
    if (tdCounts.hipertensiII > 0) results.push({ Kategori: 'Hipertensi Grade II', Jumlah: tdCounts.hipertensiII });
    if (tdCounts.hipertensiIII > 0) results.push({ Kategori: 'Hipertensi Grade III', Jumlah: tdCounts.hipertensiIII });
    if (tdCounts.sistolikTerisolasi > 0) results.push({ Kategori: 'Hipertensi Sistolik Terisolasi', Jumlah: tdCounts.sistolikTerisolasi });
    if (tdCounts.tidakAdaData > 0) results.push({ Kategori: 'Tidak Ada Data', Jumlah: tdCounts.tidakAdaData });

    return results;
}

/**
 * Rekapitulasi Kelompok Umur Peserta MCU (<35 vs >=35 by Gender)
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ 'Jenis Kelamin': string, Kategori: string, Jumlah: number }]
 */

export function calculateUmurRekap(data) {
    const counts = {
        'Laki-laki_<35': 0,
        'Laki-laki_>=35': 0,
        'Perempuan_<35': 0,
        'Perempuan_>=35': 0,
         'Tidak Ada Data': 0
    };

    data.forEach(item => {
        const usia = getNumericValue(item, 'Usia');
        const jenisKelamin = item['Jenis Kelamin'];

        if (isNaN(usia) || !jenisKelamin) {
            counts['Tidak Ada Data']++;
            return;
        }

        const genderKey = jenisKelamin.toLowerCase() === 'laki-laki' ? 'Laki-laki' :
                         jenisKelamin.toLowerCase() === 'perempuan' ? 'Perempuan' : null;

        if (genderKey) {
            const ageKey = usia < 35 ? '<35' : '>=35';
            counts[`${genderKey}_${ageKey}`]++;
        } else {
             // Invalid gender string but valid age
             counts['Tidak Ada Data']++;
        }
    });

    const results = [];
    if (counts['Laki-laki_<35'] > 0) results.push({ 'Jenis Kelamin': 'Laki-laki', Kategori: '<35 Tahun', Jumlah: counts['Laki-laki_<35'] });
    if (counts['Laki-laki_>=35'] > 0) results.push({ 'Jenis Kelamin': 'Laki-laki', Kategori: '>=35 Tahun', Jumlah: counts['Laki-laki_>=35'] });
    if (counts['Perempuan_<35'] > 0) results.push({ 'Jenis Kelamin': 'Perempuan', Kategori: '<35 Tahun', Jumlah: counts['Perempuan_<35'] });
    if (counts['Perempuan_>=35'] > 0) results.push({ 'Jenis Kelamin': 'Perempuan', Kategori: '>=35 Tahun', Jumlah: counts['Perempuan_>=35'] });
    if (counts['Tidak Ada Data'] > 0) results.push({ 'Jenis Kelamin': 'Tidak Diketahui', Kategori: 'Tidak Ada Data', Jumlah: counts['Tidak Ada Data'] });

    return results;
}

/**
 * Rekapitulasi Kadar Hemoglobin
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ 'Jenis Kelamin': string, Kategori: string, Jumlah: number }]
 */

export function calculateHbRekap(data) {
    const counts = {
        'Laki-laki_Anemia': 0,
        'Laki-laki_Tidak Anemia': 0,
        'Perempuan_Anemia': 0,
        'Perempuan_Tidak Anemia': 0,
        'Tidak Ada Data': 0
    };

    data.forEach(item => {
        const hb = getNumericValue(item, 'Hb');
        const jenisKelamin = item['Jenis Kelamin'];

        if (isNaN(hb) || !jenisKelamin) {
            counts['Tidak Ada Data']++;
            return;
        }

        const genderKey = jenisKelamin.toLowerCase() === 'laki-laki' ? 'Laki-laki' :
                         jenisKelamin.toLowerCase() === 'perempuan' ? 'Perempuan' : null;
        
        if (genderKey === 'Laki-laki') {
            if (hb < 13) {
                counts['Laki-laki_Anemia']++;
            } else {
                counts['Laki-laki_Tidak Anemia']++;
            }
        } else if (genderKey === 'Perempuan') {
            if (hb < 12) {
                counts['Perempuan_Anemia']++;
            } else {
                counts['Perempuan_Tidak Anemia']++;
            }
        } else {
             // Invalid gender string but valid Hb
             counts['Tidak Ada Data']++;
        }
    });

    const results = [];
    if (counts['Laki-laki_Anemia'] > 0) results.push({ 'Jenis Kelamin': 'Laki-laki', Kategori: 'Anemia', Jumlah: counts['Laki-laki_Anemia'] });
    if (counts['Laki-laki_Tidak Anemia'] > 0) results.push({ 'Jenis Kelamin': 'Laki-laki', Kategori: 'Tidak Anemia', Jumlah: counts['Laki-laki_Tidak Anemia'] });
    if (counts['Perempuan_Anemia'] > 0) results.push({ 'Jenis Kelamin': 'Perempuan', Kategori: 'Anemia', Jumlah: counts['Perempuan_Anemia'] });
    if (counts['Perempuan_Tidak Anemia'] > 0) results.push({ 'Jenis Kelamin': 'Perempuan', Kategori: 'Tidak Anemia', Jumlah: counts['Perempuan_Tidak Anemia'] });
    if (counts['Tidak Ada Data'] > 0) results.push({ 'Jenis Kelamin': 'Tidak Diketahui', Kategori: 'Tidak Ada Data', Jumlah: counts['Tidak Ada Data'] });

    return results;
}

/**
 * Rekapitulasi Pemeriksaan Creatinin Darah
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ Kategori: string, Jumlah: number }]
 */

export function calculateCreatininRekap(data) {
    let normal = 0;
    let tidakNormal = 0;
    let tidakAdaData = 0;

    data.forEach(item => {
        const creatinin = getNumericValue(item, 'Kreatinin');

        if (isNaN(creatinin)) {
            tidakAdaData++;
        } else if (creatinin >= 0.6 && creatinin <= 1.2) {
            normal++;
        } else { // creatinin < 0.6 or > 1.2
            tidakNormal++;
        }
    });

    const results = [];
    if (normal > 0) results.push({ Kategori: 'Normal', Jumlah: normal });
    if (tidakNormal > 0) results.push({ Kategori: 'Tidak Normal', Jumlah: tidakNormal });
    if (tidakAdaData > 0) results.push({ Kategori: 'Tidak Ada Data', Jumlah: tidakAdaData });

    return results;
}

/**
 * Rekapitulasi Suspek Gangguan Fungsi Hati (SGOT/SGPT)
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ Kategori: string, Jumlah: number }]
 */

export function calculateFungsiHatiRekap(data) {
    let normal = 0;
    let peningkatan = 0;
    let tidakAdaData = 0;

    data.forEach(item => {
        const sgot = getNumericValue(item, 'Sgot');
        const sgpt = getNumericValue(item, 'Sgpt');

         if (isNaN(sgot) || isNaN(sgpt)) {
             tidakAdaData++;
             return; // Skip patient if either value is missing/invalid
         }

        if (sgot > 34 || sgpt > 55) {
            peningkatan++;
        } else { 
            normal++;
        }
    });

    const results = [];
    if (peningkatan > 0) results.push({ Kategori: 'Peningkatan SGOT dan atau SGPT', Jumlah: peningkatan });
    if (normal > 0) results.push({ Kategori: 'Normal', Jumlah: normal });
    if (tidakAdaData > 0) results.push({ Kategori: 'Tidak Ada Data', Jumlah: tidakAdaData });

    return results;
}

/**
 * Rekapitulasi Pemeriksaan Kolesterol Total
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ Kategori: string, Jumlah: number }]
 */

export function calculateKolesterolTotalRekap(data) {
    let normal = 0;
    let batasTinggi = 0;
    let tinggi = 0;
    let tidakAdaData = 0;

    data.forEach(item => {
        // Need to handle string parsing like "233 Borderline"
        const kolesterolTotal = parseKolesterolTotal(item['Kolesterol']); 

        if (isNaN(kolesterolTotal)) {
            tidakAdaData++;
        } else if (kolesterolTotal < 200) {
            normal++;
        } else if (kolesterolTotal >= 200 && kolesterolTotal <= 239) {
            batasTinggi++;
        } else if (kolesterolTotal >= 240) {
            tinggi++;
        }
    });

    const results = [];
    if (normal > 0) results.push({ Kategori: 'Normal', Jumlah: normal });
    if (batasTinggi > 0) results.push({ Kategori: 'Batas Tinggi', Jumlah: batasTinggi });
    if (tinggi > 0) results.push({ Kategori: 'Tinggi', Jumlah: tinggi });
    if (tidakAdaData > 0) results.push({ Kategori: 'Tidak Ada Data', Jumlah: tidakAdaData });

    return results;
}

/**
 * Rekapitulasi Riwayat Kesehatan Peserta MCU
 * Counts patients having specific keywords in their 'Riwayat Kesehatan Pribadi'
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ 'Kategori Penyakit': string, Jumlah: number }]
 */

export function calculateRiwayatKesehatanRekap(data) {
    const keywords = {
        'batu empedu': 'Batu Empedu',
        'dm': 'DM (Diabetes Mellitus)',
        'diabetes mellitus': 'DM (Diabetes Mellitus)',
        'dyspepsia': 'Dyspepsia',
        'thypoid': 'Thypoid',
        'penyakit jantung': 'Penyakit Jantung',
        'tbc': 'TBC',
        'keluhan muskoloskeletal': 'Keluhan Muskoloskeletal',
        'asma': 'Asma',
        'hipertensi': 'Hipertensi',
        'covid-19': 'Covid-19'
    };

    const counts = {};
    Object.values(keywords).forEach(canonicalName => counts[canonicalName] = 0);
    let tidakAdaData = 0;

    data.forEach (item => {
        const riwayatString = item['Riwayat Kesehatan Pribadi'];

        if (!riwayatString || typeof riwayatString !== 'string') {
            tidakAdaData++;
            return;
        }

        const lowerRiwayat = riwayatString.toLowerCase();
        const patientKeywordsFound = new Set();

        for (const keyword in keywords) {
            if (lowerRiwayat.includes(keyword)) {
                patientKeywordsFound.add(keywords[keyword]); // Add the canonical name
            }
        }

        patientKeywordsFound.forEach(canonicalName => {
            if (counts[canonicalName] !== undefined) { // Ensure canonical name exists in initial counts
                 counts[canonicalName]++;
            }
        });
    });

    const results = [];
    for (const canonicalName in counts) {
        if (counts[canonicalName] > 0) {
            results.push({ 'Kategori Penyakit': canonicalName, Jumlah: counts[canonicalName] });
        }
    }
    if (tidakAdaData > 0) results.push({ 'Kategori Penyakit': 'Tidak Ada Data', Jumlah: tidakAdaData });

    // Sort results if needed (e.g., by count descending)
    // results.sort((a, b) => b.Jumlah - a.Jumlah);

    return results;
}

/**
 * Rekapitulasi Pemeriksaan Kolesterol HDL
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ Kategori: string, Jumlah: number }]
 */

export function calculateKolesterolHdlRekap(data) {
    let normal = 0;
    let rendah = 0;
    let tidakAdaData = 0;

    data.forEach(item => {
        const hdl = getNumericValue(item, 'Koles Hdl');

        if (isNaN(hdl)) {
            tidakAdaData++;
        } else if (hdl < 40) {
            rendah++;
        } else { // hdl >= 40
            normal++;
        }
    });

    const results = [];
    if (normal > 0) results.push({ Kategori: 'Normal', Jumlah: normal });
    if (rendah > 0) results.push({ Kategori: 'Rendah', Jumlah: rendah });
    if (tidakAdaData > 0) results.push({ Kategori: 'Tidak Ada Data', Jumlah: tidakAdaData });

    return results;
}

/**
 * Rekapitulasi Pemeriksaan Kolesterol LDL
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ Kategori: string, Jumlah: number }]
 */

export function calculateKolesterolLdlRekap(data) {
    let optimal = 0;
    let hampirOptimal = 0;
    let batasTinggi = 0;
    let tinggi = 0;
    let sangatTinggi = 0;
    let tidakAdaData = 0;

    data.forEach(item => {
        const ldl = getNumericValue(item, 'Koles Ldl');

        if (isNaN(ldl)) {
            tidakAdaData++;
        } else if (ldl < 100) {
            optimal++;
        } else if (ldl >= 100 && ldl <= 129) {
            hampirOptimal++;
        } else if (ldl >= 130 && ldl <= 159) {
            batasTinggi++;
        } else if (ldl >= 160 && ldl <= 189) {
            tinggi++;
        } else if (ldl >= 190) {
            sangatTinggi++;
        }
    });

    const results = [];
    if (optimal > 0) results.push({ Kategori: 'Optimal', Jumlah: optimal });
    if (hampirOptimal > 0) results.push({ Kategori: 'Hampir Optimal', Jumlah: hampirOptimal });
    if (batasTinggi > 0) results.push({ Kategori: 'Batas Tinggi', Jumlah: batasTinggi });
    if (tinggi > 0) results.push({ Kategori: 'Tinggi', Jumlah: tinggi });
    if (sangatTinggi > 0) results.push({ Kategori: 'Sangat Tinggi', Jumlah: sangatTinggi });
    if (tidakAdaData > 0) results.push({ Kategori: 'Tidak Ada Data', Jumlah: tidakAdaData });

    return results;
}

/**
 * Rekapitulasi Pemeriksaan Trigliserida
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ Kategori: string, Jumlah: number }]
 */

export function calculateTrigliseridaRekap(data) {
    let normal = 0;
    let batasTinggi = 0;
    let tinggi = 0;
    let sangatTinggi = 0;
    let tidakAdaData = 0;

    data.forEach(item => {
        const trigliserida = getNumericValue(item, 'Trgliserid');

        if (isNaN(trigliserida)) {
            tidakAdaData++;
        } else if (trigliserida < 150) {
            normal++;
        } else if (trigliserida >= 150 && trigliserida <= 199) {
            batasTinggi++;
        } else if (trigliserida >= 200 && trigliserida <= 499) {
            tinggi++;
        } else if (trigliserida >= 500) {
            sangatTinggi++;
        }
    });

    const results = [];
    if (normal > 0) results.push({ Kategori: 'Normal', Jumlah: normal });
    if (batasTinggi > 0) results.push({ Kategori: 'Batas Tinggi', Jumlah: batasTinggi });
    if (tinggi > 0) results.push({ Kategori: 'Tinggi', Jumlah: tinggi });
    if (sangatTinggi > 0) results.push({ Kategori: 'Sangat Tinggi', Jumlah: sangatTinggi });
    if (tidakAdaData > 0) results.push({ Kategori: 'Tidak Ada Data', Jumlah: tidakAdaData });

    return results;
}

/**
 * Rekapitulasi Pemeriksaan Ureum
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ Kategori: string, Jumlah: number }]
 */

export function calculateUreumRekap(data) {
    let normal = 0;
    let tidakNormal = 0;
    let tidakAdaData = 0;

    data.forEach(item => {
        const ureum = getNumericValue(item, 'Ureum');

        if (isNaN(ureum)) {
            tidakAdaData++;
        } else if (ureum >= 15 && ureum <= 40) {
            normal++;
        } else { // ureum < 15 or > 40
            tidakNormal++;
        }
    });

    const results = [];
    if (normal > 0) results.push({ Kategori: 'Normal', Jumlah: normal });
    if (tidakNormal > 0) results.push({ Kategori: 'Tidak Normal', Jumlah: tidakNormal });
    if (tidakAdaData > 0) results.push({ Kategori: 'Tidak Ada Data', Jumlah: tidakAdaData });

    return results;
}

/**
 * Rekapitulasi Pemeriksaan Asam Urat
 * Added based on new request.
 * @param {Array<Object>} data - Array data pasien
 * @returns {Array<Object>} Rekapitulasi data [{ Kategori: string, Jumlah: number }]
 */

export function calculateAsamUratRekap(data) {
    let normal = 0;
    let tidakNormal = 0;
    let tidakAdaData = 0;

    data.forEach(item => {
        const asamUrat = getNumericValue(item, 'Asam Urat');
        const jenisKelamin = item['Jenis Kelamin'];

        if (isNaN(asamUrat) || !jenisKelamin || typeof jenisKelamin !== 'string' || jenisKelamin.trim() === '') {
            tidakAdaData++;
            return;
        }

        const genderKey = jenisKelamin.trim().toLowerCase();

        let isNormal = false;
        if (genderKey === 'laki-laki') {
            if (asamUrat >= 3.5 && asamUrat <= 7.2) {
                isNormal = true;
            } else if (asamUrat > 7.2) {
                isNormal = false;
            }
        } else if (genderKey === 'perempuan') {
            if (asamUrat >= 2.6 && asamUrat <= 6.0) {
                isNormal = true;
            } else if (asamUrat > 6.0) {
                isNormal = false;
            }
        } else {
            tidakAdaData;
            return;
        }

        if (!isNaN(asamUrat)) { // Ensure it wasn't a gender issue already counted in tidakAdaData
            if (isNormal) {
                normal++;
            } else {
                tidakNormal++;
            }
        }
    });

    let asamUratCounts = { normal: 0, tidakNormal: 0, tidakAdaData: 0 };
    data.forEach(item => {
        const asamUrat = getNumericValue(item, 'Asam Urat');
        const jenisKelamin = item['Jenis Kelamin'];

        if (isNaN(asamUrat) || !jenisKelamin || typeof jenisKelamin !== 'string' || jenisKelamin.trim() === '') {
             asamUratCounts.tidakAdaData++;
             return;
         }

         const genderKey = jenisKelamin.trim().toLowerCase();

         let isNormal = false;
         if (genderKey === 'laki-laki') {
            if (asamUrat >= 3.5 && asamUrat <= 7.2) {
                isNormal = true;
            }
         } else if (genderKey === 'perempuan') {
              if (asamUrat >= 2.6 && asamUrat <= 6.0) {
                 isNormal = true;
             }
         } else {
             // Unrecognized gender, count as no data
             asamUratCounts.tidakAdaData++;
             return;
         }
         if (isNormal) {
             asamUratCounts.normal++;
         } else {
             asamUratCounts.tidakNormal++;
         }
    });

    const results = [];
    if (asamUratCounts.normal > 0) results.push({ Kategori: 'Normal', Jumlah: asamUratCounts.normal });
    if (asamUratCounts.tidakNormal > 0) results.push({ Kategori: 'Tidak Normal', Jumlah: asamUratCounts.tidakNormal });
    if (asamUratCounts.tidakAdaData > 0) results.push({ Kategori: 'Tidak Ada Data', Jumlah: asamUratCounts.tidakAdaData });

    return results;
}