import Tesseract from 'tesseract.js';

export async function performOCR(filePath: string): Promise<string> {
  if (process.env.OCR_ENABLED === 'false') return '';
  const result = await Tesseract.recognize(filePath, 'eng');
  return result.data.text || '';
}

export function parseReceiptText(text: string) {
  // Very naive parsing; in production, use better NLP rules
  const amountMatch = text.match(/(total|amount)\s*[:\-]?\s*\$?(\d+[\.,]?\d*)/i);
  const dateMatch = text.match(/(\d{4}[-\/]\d{2}[-\/]\d{2}|\d{2}[-\/]\d{2}[-\/]\d{4})/);
  const nameMatch = text.split('\n').find(l => /restaurant|cafe|hotel|store/i.test(l));
  return {
    amount: amountMatch ? parseFloat(amountMatch[2].replace(',', '')) : undefined,
    date: dateMatch ? new Date(dateMatch[0]) : undefined,
    merchant: nameMatch || undefined,
    description: text.substring(0, 200)
  };
}
