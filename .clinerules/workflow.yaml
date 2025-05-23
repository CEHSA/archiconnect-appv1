name: ArchitexProjectLifecycle
description: Workflow describing the project lifecycle from initial client contact to final payment and project completion, based on Architex's Terms and Conditions and Privacy Policy.
actors:
  - Client
  - Admin
  - Freelancer
stages:
  - name: AccountRegistrationAndMeetingScheduling
    description: Client registers an account on the Architex website or System and schedules an initial meeting. This involves providing personal identification data such as name, email, phone, company name, and job title. (Privacy Policy 1.1; Terms and Conditions 3.1, 4.1)
    actor: Client
    actions:
      - Register account providing personal identification data
      - Schedule initial meeting
    next_stages:
      - InitialConsultationAndProjectSetup
    is_start_stage: true
    is_final_stage: false

  - name: InitialConsultationAndProjectSetup
    description: Admin conducts an initial meeting (which doubles as a basic system training session) with the Client. They collaboratively define project scope (briefs, design requirements), timeline, initial hourly rate, and a 'not-to-exceed' budget. Admin then creates the job/project within the System. Project and service information is collected here. (Privacy Policy 1.2; Terms and Conditions 4.1)
    actor: Admin
    actions:
      - Conduct initial meeting and system training
      - Collaboratively define project scope, timeline, and budget
      - Create project/job in the System
    next_stages:
      - FreelancerAssignment
    is_start_stage: false
    is_final_stage: false

  - name: FreelancerAssignment
    description: Once a project is set up, Admin sends automatic notifications to designated internal freelancers. Freelancers review the job details and indicate their availability. Admin then assigns the appropriate freelancer(s) to the project. (Terms and Conditions 4.2)
    actor: Admin
    actions:
      - Notify designated freelancers of the new project
      - Receive freelancer availability status
      - Assign suitable freelancer(s) to the project
    next_stages:
      - WorkExecutionAndMonitoring
    is_start_stage: false
    is_final_stage: false

  - name: WorkExecutionAndMonitoring
    description: Assigned freelancer(s) execute the project tasks and log their working hours using the System's compulsory time tracking features (including an automatic stop function). Progress reports are generated to monitor efficiency. (Terms and Conditions 4.3)
    actor: Freelancer
    actions:
      - Execute project tasks as per requirements
      - Log working hours accurately using System time tracking
    next_stages:
      - WorkSubmissionByFreelancer
    is_start_stage: false
    is_final_stage: false

  - name: WorkSubmissionByFreelancer
    description: Freelancer submits completed work, including deliverable files and timesheets, within the System. (Terms and Conditions 4.4)
    actor: Freelancer
    actions:
      - Upload/submit deliverable files
      - Submit timesheets for work performed
    next_stages:
      - InternalAdminReview
    is_start_stage: false
    is_final_stage: false

  - name: InternalAdminReview
    description: Admin reviews the freelancer's submitted work, including deliverable files and timesheets, for quality and budget compliance before forwarding relevant communications and deliverables to the Client. (Terms and Conditions 4.4)
    actor: Admin
    actions:
      - Review freelancer's work for quality standards
      - Check submission against budget compliance
      - Forward work/communications to Client if satisfactory
    next_stages:
      - ClientReviewWork
    is_start_stage: false
    is_final_stage: false

  - name: ClientReviewWork
    description: Client reviews the submitted work against the agreed 'not-to-exceed' budget limit. Based on the review, the Client can approve the work or request revisions. (Terms and Conditions 4.5)
    actor: Client
    actions:
      - Review submitted work and deliverables
      - Compare work against project scope and budget
      - Decide to approve or request revisions
    next_stages:
      - FinalApprovalAndPaymentProcessing # if work is approved
      - ProcessClientRevisions # if revisions are requested
    is_start_stage: false
    is_final_stage: false

  - name: ProcessClientRevisions
    description: If the Client requests revisions, these are communicated through the Admin to the freelancer. If additional time or funds are required due to these revisions, scope changes, or unforeseen complexities, freelancers must submit an appeal. This appeal is reviewed by both the Client and Admin, and (if approved) is reflected in an updated budget. Work then continues. (Terms and Conditions 4.5, 8.2)
    actor: Admin # Admin facilitates this process
    actions:
      - Communicate Client's revision requests to the freelancer
      - Manage freelancer's budget appeal submission if any
      - Facilitate Client and Admin review of budget appeal
      - Oversee budget update in the System if appeal is approved
    next_stages:
      - WorkExecutionAndMonitoring # Freelancer works on revisions
    is_start_stage: false
    is_final_stage: false

  - name: FinalApprovalAndPaymentProcessing
    description: Client provides final work approval via the System. After approval, Admin marks the project as completed and processes payments according to the actual approved logging of hours, agreed hourly rate, and the 'not-to-exceed' budget defined during project setup or updated via appeal. (Terms and Conditions 4.6, 8.3)
    actor: Admin # Client gives approval, Admin processes completion and payment.
    actions:
      - Receive final approval from Client via System
      - Mark project as completed in the System
      - Process payments based on approved hours and budget
    next_stages: [] # This is the final stage for a specific project lifecycle.
    is_start_stage: false
    is_final_stage: true