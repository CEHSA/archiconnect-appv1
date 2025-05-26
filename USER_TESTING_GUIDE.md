# Comprehensive User Group Testing Guide

## Test Credentials

### Admin User (Admin Guard)
- **Email:** `admin@architex.co.za`
- **Password:** `password`
- **Login URL:** `http://localhost:8000/admin/login`

### Client Users (Web Guard)
- **Email:** `client1@architex.co.za`, `client2@architex.co.za`, `client3@architex.co.za`
- **Password:** `password`
- **Login URL:** `http://localhost:8000/login`

### Freelancer User (Web Guard)
- **Email:** `freelancer@architex.co.za`
- **Password:** `password`
- **Login URL:** `http://localhost:8000/login`

---

## 1. ADMIN USER TESTING

### Step 1: Admin Login
1. Navigate to: `http://localhost:8000/admin/login`
2. Login with: `admin@architex.co.za` / `password`
3. ✅ Should redirect to admin dashboard

### Step 2: Admin Dashboard
URL: `http://localhost:8000/admin/dashboard`
**Expected Features:**
- ✅ Recent activity logs
- ✅ System overview statistics
- ✅ Quick action buttons
- ✅ Admin-specific navigation menu

### Step 3: User Management
URL: `http://localhost:8000/admin/users`
**Tests to Perform:**
- ✅ View list of all users (clients & freelancers)
- ✅ Search users by name/email
- ✅ Filter users by role (client/freelancer)
- ✅ Create new user (test client creation)
- ✅ Edit existing user
- ✅ Delete user (optional - be careful)

**Create User Test:**
1. Click "Create User" button
2. Fill form with test data:
   - Name: `Test Client Admin`
   - Email: `testclient@architex.co.za`
   - Role: `client`
   - Password: `password`
3. ✅ Should create user and show in activity log

### Step 4: Job Management
URL: `http://localhost:8000/admin/jobs`
**Tests to Perform:**
- ✅ View all jobs from all clients
- ✅ Create job on behalf of client
- ✅ Edit any job
- ✅ Delete job
- ✅ View job details

**Create Job Test:**
1. Click "Create Job" button
2. Fill form:
   - Title: `Admin Created Test Job`
   - Client: Select a client
   - Description: Rich text content
   - Budget: `2000`
   - Hourly Rate: `50`
   - Skills: `Laravel, PHP, Vue.js`
   - Status: `open`
3. ✅ Should create job and appear in activity log

### Step 5: Job Assignments
URL: `http://localhost:8000/admin/job-assignments`
**Tests to Perform:**
- ✅ View all job assignments
- ✅ Create new assignment (assign freelancer to job)
- ✅ Edit assignment status
- ✅ Add admin remarks

### Step 6: Reports
URL: `http://localhost:8000/admin/reports`
**Tests to Perform:**
- ✅ Job Progress Report
- ✅ Freelancer Performance Report
- ✅ Client Project Status Report
- ✅ Financial Reports

### Step 7: Admin Settings
URL: `http://localhost:8000/admin/settings`
**Tests to Perform:**
- ✅ View system settings
- ✅ Update admin profile

---

## 2. CLIENT USER TESTING

### Step 1: Client Login
1. Navigate to: `http://localhost:8000/login`
2. Login with: `client1@architex.co.za` / `password`
3. ✅ Should redirect to client dashboard

### Step 2: Client Dashboard
URL: `http://localhost:8000/dashboard`
**Expected Features:**
- ✅ Client-specific dashboard
- ✅ My jobs overview
- ✅ Recent proposals
- ✅ Client navigation menu (no admin options)

### Step 3: Job Management (Client)
URL: `http://localhost:8000/client/jobs`
**Tests to Perform:**
- ✅ View only MY jobs (not other clients' jobs)
- ✅ Create new job
- ✅ Edit my own jobs
- ✅ Delete my own jobs
- ❌ Should NOT see other clients' jobs

**Create Job Test:**
1. Click "Create Job" or navigate to `/client/jobs/create`
2. Fill form:
   - Title: `Client Test Job`
   - Description: `Test job description`
   - Budget: `1500`
   - Skills: `PHP, Laravel`
3. ✅ Should create job successfully

### Step 4: Proposals Management
URL: `http://localhost:8000/client/jobs/{job}/proposals`
**Tests to Perform:**
- ✅ View proposals for MY jobs only
- ✅ Accept/reject proposals
- ✅ Message freelancers
- ❌ Should NOT see proposals for other clients' jobs

### Step 5: Work Submissions
URL: `http://localhost:8000/client/work-submissions`
**Tests to Perform:**
- ✅ Review work submissions
- ✅ Approve/request revisions
- ✅ Add client remarks

### Step 6: Access Control Tests
**Should FAIL (403 Forbidden):**
- ❌ `http://localhost:8000/admin/dashboard`
- ❌ `http://localhost:8000/admin/users`
- ❌ `http://localhost:8000/admin/jobs`
- ❌ Other admin routes

---

## 3. FREELANCER USER TESTING

### Step 1: Freelancer Login
1. Navigate to: `http://localhost:8000/login`
2. Login with: `freelancer@architex.co.za` / `password`
3. ✅ Should redirect to freelancer dashboard

### Step 2: Freelancer Dashboard
URL: `http://localhost:8000/dashboard`
**Expected Features:**
- ✅ Freelancer-specific dashboard
- ✅ Available jobs overview
- ✅ My assignments
- ✅ Recent activity
- ✅ Freelancer navigation menu

### Step 3: Browse Jobs
URL: `http://localhost:8000/freelancer/jobs`
**Tests to Perform:**
- ✅ View all open/available jobs
- ✅ Filter jobs by criteria
- ✅ View job details
- ✅ Submit proposals

**Submit Proposal Test:**
1. Find an open job
2. Click "Submit Proposal"
3. Fill form:
   - Proposal text: `I am interested in this project...`
   - Bid amount: `800`
   - Timeline: `2 weeks`
4. ✅ Should submit proposal successfully

### Step 4: My Assignments
URL: `http://localhost:8000/freelancer/assignments`
**Tests to Perform:**
- ✅ View MY assignments only
- ✅ Accept/decline assignments
- ✅ View assignment details
- ✅ Submit work
- ❌ Should NOT see other freelancers' assignments

### Step 5: Time Logging
URL: `http://localhost:8000/freelancer/assignments/{assignment}/time-logs`
**Tests to Perform:**
- ✅ Log time for my assignments
- ✅ Add task descriptions
- ✅ View time log history

### Step 6: Work Submissions
URL: `http://localhost:8000/freelancer/assignments/{assignment}/submissions`
**Tests to Perform:**
- ✅ Submit work for assignments
- ✅ Upload files
- ✅ Add submission notes
- ✅ View submission status

### Step 7: Access Control Tests
**Should FAIL (403 Forbidden):**
- ❌ `http://localhost:8000/admin/dashboard`
- ❌ `http://localhost:8000/admin/users`
- ❌ `http://localhost:8000/client/jobs/create`
- ❌ Other admin/client-specific routes

---

## 4. CROSS-USER TESTING

### Test Data Isolation
1. **As Client1:** Create a job
2. **As Client2:** Try to access Client1's job ❌ Should FAIL
3. **As Freelancer1:** Submit proposal to Client1's job ✅ Should WORK
4. **As Admin:** View all jobs and proposals ✅ Should WORK

### Test Role-Based Permissions
1. **Navigation Menus:** Each user type should see different menu items
2. **Dashboard Content:** Each user should see role-appropriate content
3. **Route Access:** Users should only access their authorized routes
4. **Data Visibility:** Users should only see their own data (except admins)

---

## 5. NOTIFICATION TESTING

### Email Notifications (if configured)
- ✅ Job assignment notifications
- ✅ Proposal submission notifications
- ✅ Work submission notifications

### Database Notifications
- ✅ Check notification icon/count in navigation
- ✅ Mark notifications as read
- ✅ View notification details

---

## 6. ERROR HANDLING TESTING

### Unauthorized Access
- Try accessing admin routes as client/freelancer
- Try accessing other users' private data
- Verify proper 403/404 error pages

### Data Validation
- Submit forms with invalid data
- Verify proper error messages
- Test file upload limits

---

## TESTING CHECKLIST

Use this checklist while testing:

### Admin User ✅
- [ ] Login successful
- [ ] Dashboard loads
- [ ] User management works
- [ ] Job management works
- [ ] Can create users
- [ ] Can create jobs
- [ ] Can assign freelancers
- [ ] Reports accessible
- [ ] Activity logging works

### Client User ✅
- [ ] Login successful
- [ ] Client dashboard loads
- [ ] Can create jobs
- [ ] Can view own jobs only
- [ ] Can manage proposals
- [ ] Can review work submissions
- [ ] Cannot access admin routes
- [ ] Cannot see other clients' data

### Freelancer User ✅
- [ ] Login successful
- [ ] Freelancer dashboard loads
- [ ] Can browse jobs
- [ ] Can submit proposals
- [ ] Can view assignments
- [ ] Can log time
- [ ] Can submit work
- [ ] Cannot access admin/client routes
- [ ] Cannot see other freelancers' data

### Security ✅
- [ ] Role-based access control working
- [ ] Data isolation working
- [ ] Proper error pages for unauthorized access
- [ ] No sensitive data leaking between users

---

## NOTES
- Test each functionality thoroughly
- Verify error messages are user-friendly
- Check responsive design on different screen sizes
- Test with both valid and invalid data
- Verify all notifications work properly
