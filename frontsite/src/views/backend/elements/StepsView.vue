<script setup></script>

<template>
  <!-- Hero -->
  <BasePageHeading
    title="Steps"
    subtitle="Elements for creating buttons for your form wizards."
  >
    <template #extra>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="javascript:void(0)">Elements</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">Steps</li>
        </ol>
      </nav>
    </template>
  </BasePageHeading>
  <!-- END Hero -->

  <!-- Page Content -->
  <div class="content">
    <!-- Simple -->
    <BaseBlock title="Simple" content-full content-class="space-y-3">
      <nav
        class="d-flex flex-column flex-md-row items-center justify-content-between gap-2"
      >
        <a
          href="javascript:void(0)"
          class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-2"
        >
          <div>
            <div>Step 1</div>
            <div class="fw-normal">Registration</div>
          </div>
          <div class="text-primary">
            <i class="fa fa-fw fa-check"></i>
          </div>
        </a>
        <a
          href="javascript:void(0)"
          class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-2"
        >
          <div>
            <div class="text-primary">Step 2</div>
            <div class="fw-normal">Personal Info</div>
          </div>
          <div class="opacity-25">
            <i class="fa fa-fw fa-check"></i>
          </div>
        </a>
        <a
          href="javascript:void(0)"
          class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-2"
        >
          <div>
            <div>Step 3</div>
            <div class="fw-normal">Completion</div>
          </div>
          <div class="opacity-25">
            <i class="fa fa-fw fa-check"></i>
          </div>
        </a>
      </nav>
      <div
        class="rounded-2 py-8 bg-body-light text-muted fs-sm d-flex align-items-center justify-content-center"
      >
        Content...
      </div>
    </BaseBlock>
    <!-- END Simple -->

    <!-- Simple with progress -->
    <BaseBlock
      title="Simple"
      subtitle="with progress"
      content-full
      content-class="space-y-3"
    >
      <div>
        <div
          class="progress mb-2"
          style="height: 10px"
          role="progressbar"
          aria-valuenow="66.6"
          aria-valuemin="0"
          aria-valuemax="100"
        >
          <div class="progress-bar" style="width: 66.6%"></div>
        </div>
        <nav
          class="d-flex flex-column flex-md-row items-center justify-content-between gap-2"
        >
          <a
            href="javascript:void(0)"
            class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-2"
          >
            <div>
              <div>Step 1</div>
              <div class="fw-normal">Registration</div>
            </div>
            <div class="text-primary">
              <i class="fa fa-fw fa-check"></i>
            </div>
          </a>
          <a
            href="javascript:void(0)"
            class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-2"
          >
            <div>
              <div class="text-primary">Step 2</div>
              <div class="fw-normal">Personal Info</div>
            </div>
            <div class="opacity-25">
              <i class="fa fa-fw fa-check"></i>
            </div>
          </a>
          <a
            href="javascript:void(0)"
            class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-2"
          >
            <div>
              <div>Step 3</div>
              <div class="fw-normal">Completion</div>
            </div>
            <div class="opacity-25">
              <i class="fa fa-fw fa-check"></i>
            </div>
          </a>
        </nav>
      </div>
      <div
        class="rounded-2 py-8 bg-body-light text-muted fs-sm d-flex align-items-center justify-content-center"
      >
        Content...
      </div>
    </BaseBlock>
    <!-- END Simple with progress -->

    <!-- Alternate -->
    <BaseBlock title="Alternate" content-full content-class="space-y-3">
      <nav
        class="d-flex flex-column flex-lg-row items-center justify-content-between gap-2"
      >
        <a
          href="javascript:void(0)"
          class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-3"
        >
          <div
            class="flex-grow-0 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
            style="width: 36px; height: 36px"
          >
            <i class="fa fa-fw fa-check"></i>
          </div>
          <div class="flex-grow-1">
            <div>Registration</div>
            <div class="fw-normal">Email and password</div>
          </div>
        </a>
        <a
          href="javascript:void(0)"
          class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-3"
        >
          <div
            class="flex-grow-0 rounded-circle border border-3 border-primary d-flex align-items-center justify-content-center"
            style="width: 36px; height: 36px"
          >
            2
          </div>
          <div class="flex-grow-1">
            <div class="text-primary">Personal Info</div>
            <div class="fw-normal">Name and address</div>
          </div>
        </a>
        <a
          href="javascript:void(0)"
          class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-3"
        >
          <div
            class="flex-grow-0 rounded-circle bg-body-dark d-flex align-items-center justify-content-center"
            style="width: 36px; height: 36px"
          >
            3
          </div>
          <div class="flex-grow-1">
            <div>Completion</div>
            <div class="fw-normal">Preview your account</div>
          </div>
        </a>
      </nav>
      <div
        class="rounded-2 py-8 bg-body-light text-muted fs-sm d-flex align-items-center justify-content-center"
      >
        Content...
      </div>
    </BaseBlock>
    <!-- END Alternate -->

    <!-- Alternate with progress -->
    <BaseBlock
      title="Alternate"
      subtitle="with progress"
      content-full
      content-class="space-y-3"
    >
      <div>
        <div
          class="progress mb-2"
          style="height: 10px"
          role="progressbar"
          aria-valuenow="66.6"
          aria-valuemin="0"
          aria-valuemax="100"
        >
          <div class="progress-bar" style="width: 66.6%"></div>
        </div>
        <nav
          class="d-flex flex-column flex-lg-row items-center justify-content-between gap-2"
        >
          <a
            href="javascript:void(0)"
            class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-3"
          >
            <div
              class="flex-grow-0 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
              style="width: 36px; height: 36px"
            >
              <i class="fa fa-fw fa-check"></i>
            </div>
            <div class="flex-grow-1">
              <div>Registration</div>
              <div class="fw-normal">Email and password</div>
            </div>
          </a>
          <a
            href="javascript:void(0)"
            class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-3"
          >
            <div
              class="flex-grow-0 rounded-circle border border-3 border-primary d-flex align-items-center justify-content-center"
              style="width: 36px; height: 36px"
            >
              2
            </div>
            <div class="flex-grow-1">
              <div class="text-primary">Personal Info</div>
              <div class="fw-normal">Name and address</div>
            </div>
          </a>
          <a
            href="javascript:void(0)"
            class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-3"
          >
            <div
              class="flex-grow-0 rounded-circle bg-body-dark d-flex align-items-center justify-content-center"
              style="width: 36px; height: 36px"
            >
              3
            </div>
            <div class="flex-grow-1">
              <div>Completion</div>
              <div class="fw-normal">Preview your account</div>
            </div>
          </a>
        </nav>
      </div>
      <div
        class="rounded-2 py-8 bg-body-light text-muted fs-sm d-flex align-items-center justify-content-center"
      >
        Content...
      </div>
    </BaseBlock>
    <!-- END Alternate with progress -->
  </div>
  <!-- END Page Content -->
</template>
