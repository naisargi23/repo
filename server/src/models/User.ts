import mongoose, { Schema, Document, Types } from 'mongoose';
import { Role } from '../types.js';

export interface IUser extends Document {
  email: string;
  passwordHash: string;
  name: string;
  company: Types.ObjectId;
  roles: Role[];
  manager?: Types.ObjectId | null; // direct manager
  isManagerApprover: boolean; // whether manager is first approver
  createdAt: Date;
}

const UserSchema = new Schema<IUser>({
  email: { type: String, required: true, unique: true, index: true },
  passwordHash: { type: String, required: true },
  name: { type: String, required: true },
  company: { type: Schema.Types.ObjectId, ref: 'Company', required: true, index: true },
  roles: { type: [String], enum: ['ADMIN', 'MANAGER', 'EMPLOYEE'], default: ['EMPLOYEE'] },
  manager: { type: Schema.Types.ObjectId, ref: 'User', default: null },
  isManagerApprover: { type: Boolean, default: true },
  createdAt: { type: Date, default: Date.now }
});

export default mongoose.model<IUser>('User', UserSchema);
