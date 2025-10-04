import axios from 'axios';

export async function fetchExchangeRates(base: string) {
  const url = `${process.env.EXCHANGE_API_BASE || 'https://api.exchangerate-api.com/v4/latest'}/${base}`;
  const { data } = await axios.get(url);
  return data; // { rates: { USD: 1, ... } }
}

export function convertAmount(amount: number, from: string, to: string, ratesData: any): number {
  if (from === to) return amount;
  const rates = ratesData.rates || {};
  if (rates[to] && rates[from]) {
    const amountInBase = amount / rates[from];
    return amountInBase * rates[to];
  }
  if (rates[to]) {
    return amount * rates[to];
  }
  throw new Error('Unsupported currency conversion');
}
