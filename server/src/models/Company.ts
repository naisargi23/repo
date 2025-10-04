import mongoose, { Schema, Document } from 'mongoose';

export interface ICompany extends Document {
  name: string;
  country: string; // country name
  currencyCode: string; // e.g., USD, INR
  createdAt: Date;
}

const CompanySchema = new Schema<ICompany>({
  name: { type: String, required: true },
  country: { type: String, required: true },
  currencyCode: { type: String, required: true },
  createdAt: { type: Date, default: Date.now }
});

export default mongoose.model<ICompany>('Company', CompanySchema);
