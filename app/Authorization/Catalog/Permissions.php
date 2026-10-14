<?php

namespace App\Authorization\Catalog;

final class Permissions
{
    public const JOB_VIEW = 'job.view';
    public const JOB_VIEW_UNPUBLISHED = 'job.view.unpublished';
    public const JOB_CREATE = 'job.create';
    public const JOB_UPDATE = 'job.update';
    public const JOB_DELETE = 'job.delete';
    public const JOB_SECTION_FINANCIAL = 'job.section.financial';
    public const JOB_FIELDS_PUBLIC_WRITE = 'job.fields.public.write';
    public const JOB_FIELDS_FINANCIAL_READ = 'job.fields.financial.read';
    public const JOB_FIELDS_FINANCIAL_WRITE = 'job.fields.financial.write';

    public const APPLICATION_VIEW = 'application.view';
    public const APPLICATION_VIEW_ANY = 'application.view.any';
    public const APPLICATION_CREATE = 'application.create';
    public const APPLICATION_UPDATE = 'application.update';
    public const APPLICATION_DELETE = 'application.delete';
    public const APPLICATION_SECTION_INTERNAL = 'application.section.internal';
    public const APPLICATION_FIELDS_PUBLIC_WRITE = 'application.fields.public.write';
    public const APPLICATION_FIELDS_WORKFLOW_WRITE = 'application.fields.workflow.write';
    public const APPLICATION_FIELDS_INTERNAL_READ = 'application.fields.internal.read';
    public const APPLICATION_FIELDS_INTERNAL_WRITE = 'application.fields.internal.write';
}
