<?php

namespace App\Authorization\Catalog;

class PermissionCatalog
{
    /**
     * @return list<PermissionDefinition>
     */
    public function definitions(): array
    {
        return [
            new PermissionDefinition(Permissions::JOB_VIEW, Layer::Page, 'job', 'Open job listing and detail pages'),
            new PermissionDefinition(Permissions::JOB_VIEW_UNPUBLISHED, Layer::Action, 'job', 'See draft and closed job postings'),
            new PermissionDefinition(Permissions::JOB_CREATE, Layer::Action, 'job', 'Create job postings'),
            new PermissionDefinition(Permissions::JOB_UPDATE, Layer::Action, 'job', 'Update job postings'),
            new PermissionDefinition(Permissions::JOB_DELETE, Layer::Action, 'job', 'Delete job postings'),
            new PermissionDefinition(Permissions::JOB_SECTION_FINANCIAL, Layer::Section, 'job', 'See the financials section'),
            new PermissionDefinition(Permissions::JOB_FIELDS_PUBLIC_WRITE, Layer::Field, 'job', 'Write public job fields'),
            new PermissionDefinition(Permissions::JOB_FIELDS_FINANCIAL_READ, Layer::Field, 'job', 'Read salary and hiring budget'),
            new PermissionDefinition(Permissions::JOB_FIELDS_FINANCIAL_WRITE, Layer::Field, 'job', 'Write salary and hiring budget'),

            new PermissionDefinition(Permissions::APPLICATION_VIEW, Layer::Page, 'application', 'Open application listing and detail pages'),
            new PermissionDefinition(Permissions::APPLICATION_VIEW_ANY, Layer::Action, 'application', 'See applications submitted by other users'),
            new PermissionDefinition(Permissions::APPLICATION_CREATE, Layer::Action, 'application', 'Submit applications'),
            new PermissionDefinition(Permissions::APPLICATION_UPDATE, Layer::Action, 'application', 'Update applications'),
            new PermissionDefinition(Permissions::APPLICATION_DELETE, Layer::Action, 'application', 'Delete applications'),
            new PermissionDefinition(Permissions::APPLICATION_SECTION_INTERNAL, Layer::Section, 'application', 'See the internal review section'),
            new PermissionDefinition(Permissions::APPLICATION_FIELDS_PUBLIC_WRITE, Layer::Field, 'application', 'Write cover letter and resume'),
            new PermissionDefinition(Permissions::APPLICATION_FIELDS_WORKFLOW_WRITE, Layer::Field, 'application', 'Write application status'),
            new PermissionDefinition(Permissions::APPLICATION_FIELDS_INTERNAL_READ, Layer::Field, 'application', 'Read internal notes and score'),
            new PermissionDefinition(Permissions::APPLICATION_FIELDS_INTERNAL_WRITE, Layer::Field, 'application', 'Write internal notes and score'),
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public function rolePermissions(): array
    {
        return [
            Roles::APPLICANT => [
                Permissions::JOB_VIEW,
                Permissions::APPLICATION_VIEW,
                Permissions::APPLICATION_CREATE,
                Permissions::APPLICATION_UPDATE,
                Permissions::APPLICATION_FIELDS_PUBLIC_WRITE,
            ],
            Roles::MANAGER => [
                Permissions::JOB_VIEW,
                Permissions::JOB_VIEW_UNPUBLISHED,
                Permissions::JOB_CREATE,
                Permissions::JOB_UPDATE,
                Permissions::JOB_SECTION_FINANCIAL,
                Permissions::JOB_FIELDS_PUBLIC_WRITE,
                Permissions::JOB_FIELDS_FINANCIAL_READ,
                Permissions::JOB_FIELDS_FINANCIAL_WRITE,
                Permissions::APPLICATION_VIEW,
                Permissions::APPLICATION_VIEW_ANY,
                Permissions::APPLICATION_UPDATE,
                Permissions::APPLICATION_SECTION_INTERNAL,
                Permissions::APPLICATION_FIELDS_WORKFLOW_WRITE,
                Permissions::APPLICATION_FIELDS_INTERNAL_READ,
                Permissions::APPLICATION_FIELDS_INTERNAL_WRITE,
            ],
            Roles::ADMIN => array_map(
                fn (PermissionDefinition $definition) => $definition->key,
                $this->definitions(),
            ),
        ];
    }

    public function page(string $key): Page
    {
        $pages = $this->pages();

        if (! isset($pages[$key])) {
            throw new \InvalidArgumentException("Unknown page [{$key}].");
        }

        return $pages[$key];
    }

    /**
     * @return array<string, Page>
     */
    public function pages(): array
    {
        return [
            'job.index' => new Page('job.index', 'job', Permissions::JOB_VIEW),
            'job.show' => new Page('job.show', 'job', Permissions::JOB_VIEW),
            'job.store' => new Page('job.store', 'job', Permissions::JOB_CREATE),
            'job.update' => new Page('job.update', 'job', Permissions::JOB_UPDATE),
            'job.destroy' => new Page('job.destroy', 'job', Permissions::JOB_DELETE),

            'application.index' => new Page('application.index', 'application', Permissions::APPLICATION_VIEW),
            'application.show' => new Page('application.show', 'application', Permissions::APPLICATION_VIEW),
            'application.store' => new Page('application.store', 'application', Permissions::APPLICATION_CREATE),
            'application.update' => new Page('application.update', 'application', Permissions::APPLICATION_UPDATE),
            'application.destroy' => new Page('application.destroy', 'application', Permissions::APPLICATION_DELETE),
        ];
    }

    /**
     * @return list<Section>
     */
    public function sectionsFor(string $pageKey): array
    {
        return $this->sections()[$pageKey] ?? [];
    }

    /**
     * @return array<string, list<Section>>
     */
    public function sections(): array
    {
        return [
            'job.show' => [
                new Section('overview', 'job.show', Permissions::JOB_VIEW),
                new Section('financials', 'job.show', Permissions::JOB_SECTION_FINANCIAL),
            ],
            'application.show' => [
                new Section('overview', 'application.show', Permissions::APPLICATION_VIEW),
                new Section('internal_review', 'application.show', Permissions::APPLICATION_SECTION_INTERNAL),
            ],
        ];
    }

    /**
     * @return list<FieldSet>
     */
    public function fieldSetsFor(string $resource): array
    {
        return $this->fieldSets()[$resource] ?? [];
    }

    /**
     * @return array<string, list<FieldSet>>
     */
    public function fieldSets(): array
    {
        return [
            'job' => [
                new FieldSet(
                    'public',
                    'job',
                    ['title', 'description', 'location', 'employment_type', 'status'],
                    Permissions::JOB_VIEW,
                    null,
                ),
                new FieldSet(
                    'public_write',
                    'job',
                    ['title', 'description', 'location', 'employment_type', 'status'],
                    Permissions::JOB_VIEW,
                    Permissions::JOB_FIELDS_PUBLIC_WRITE,
                ),
                new FieldSet(
                    'financial',
                    'job',
                    ['salary_min', 'salary_max', 'hiring_budget'],
                    Permissions::JOB_FIELDS_FINANCIAL_READ,
                    Permissions::JOB_FIELDS_FINANCIAL_WRITE,
                ),
            ],
            'application' => [
                new FieldSet(
                    'public',
                    'application',
                    ['job_id', 'user_id', 'cover_letter', 'resume_url', 'status'],
                    Permissions::APPLICATION_VIEW,
                    null,
                ),
                new FieldSet(
                    'public_write',
                    'application',
                    ['cover_letter', 'resume_url'],
                    Permissions::APPLICATION_VIEW,
                    Permissions::APPLICATION_FIELDS_PUBLIC_WRITE,
                ),
                new FieldSet(
                    'workflow',
                    'application',
                    ['status'],
                    Permissions::APPLICATION_VIEW,
                    Permissions::APPLICATION_FIELDS_WORKFLOW_WRITE,
                ),
                new FieldSet(
                    'internal',
                    'application',
                    ['internal_notes', 'score'],
                    Permissions::APPLICATION_FIELDS_INTERNAL_READ,
                    Permissions::APPLICATION_FIELDS_INTERNAL_WRITE,
                ),
            ],
        ];
    }
}
