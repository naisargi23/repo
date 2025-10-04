export type Role = 'ADMIN' | 'MANAGER' | 'EMPLOYEE';

export interface JwtPayloadUser {
  id: string;
  companyId: string;
  roles: Role[];
}
