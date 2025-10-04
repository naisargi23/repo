# Expense Reimbursement System with Multi-level Approvals

A full-stack app using Node/Express, MongoDB (Mongoose), and React (Vite) to manage expense submissions, approvals with multi-step and conditional rules, and OCR for receipts.

## Features
- Authentication with JWT; first signup auto-creates Company and Admin user
- Company currency resolved by country via REST Countries API
- User management: Admin assigns roles (Employee, Manager), manager relationships, manager-as-first-approver
- Expense submission with OCR receipt upload (tesseract.js)
- Multi-step approvals with configurable steps and conditional rules (percentage, specific approver, hybrid)
- Manager approvals list, approve/reject with comments
- Currency conversion util (exchange-rate-api)

## Tech Stack
- Server: Node.js, Express, TypeScript, Mongoose, Multer, Tesseract.js
- Client: React, Vite, React Router, Axios
- DB: MongoDB

## File Structure
```
server/
  package.json
  tsconfig.json
  .env.example
  src/
    index.ts
    types.d.ts
    middleware/
      auth.ts
    models/
      User.ts
      Company.ts
      ApprovalFlow.ts
      Expense.ts
    routes/
      auth.ts
      users.ts
      expenses.ts
      approvals.ts
      config.ts
    services/
      approvalEngine.ts
    utils/
      currency.ts
      ocr.ts
client/
  package.json
  tsconfig.json
  vite.config.ts
  index.html
  src/
    main.tsx
    App.tsx
    api.ts
    pages/
      Login.tsx
      Signup.tsx
      Dashboard.tsx
      SubmitExpense.tsx
      MyExpenses.tsx
      Approvals.tsx
      Admin.tsx
```

## Prerequisites
- Node.js >= 18
- MongoDB running locally (or provide `MONGO_URI`)

## Setup
1. Clone repo and open terminal in project root.
2. Server setup:
   ```bash
   cd server
   cp .env.example .env
   npm install
   npm run dev
   ```
3. Client setup:
   ```bash
   cd ../client
   npm install
   npm run dev
   ```
4. Open `http://localhost:5173` in your browser.

## Environment Variables (server/.env)
- `PORT` API port (default 4000)
- `MONGO_URI` Mongo connection string
- `JWT_SECRET` Secret for signing JWTs
- `CLIENT_URL` CORS allowlist (comma-separated)
- `EXCHANGE_API_BASE` Exchange rates base URL
- `RESTCOUNTRIES_URL` Countries API URL
- `UPLOAD_DIR` Directory for uploads
- `OCR_ENABLED` Set to `false` to disable OCR

## Usage Flow
- Signup to create your company and initial Admin.
- As Admin: create users, set roles and manager, configure approval flows.
- Employees: submit expenses (attach receipt for OCR), view history.
- Approvers (Manager/Admin/Role-based): review items in Approvals and approve/reject.

## Notes
- For OCR, `tesseract.js` runs in-process; for heavy use consider a dedicated OCR service.
- Currency conversion utility provided; you can enhance by caching exchange rates.
- This is a reference implementation; add validation, pagination, rate limiting, and production-hardening as needed.
