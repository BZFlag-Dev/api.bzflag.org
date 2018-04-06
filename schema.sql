CREATE TABLE public.users (
    id serial NOT NULL,
    bzid varchar NOT NULL,
    CONSTRAINT users_pk PRIMARY KEY (id),
    CONSTRAINT users_bzid_un UNIQUE (bzid)
)
WITH (
OIDS=FALSE
) ;

CREATE TABLE public.organizations (
    id serial NOT NULL,
    founder int4 NOT NULL,
    short_name varchar NOT NULL,
    display_name varchar NOT NULL,
    CONSTRAINT organizations_pk PRIMARY KEY (id),
    CONSTRAINT organizations_un UNIQUE (short_name),
    CONSTRAINT organizations_users_fk FOREIGN KEY (founder) REFERENCES users(id) ON UPDATE RESTRICT ON DELETE RESTRICT
)
WITH (
OIDS=FALSE
) ;

CREATE TABLE public.users_organizations (
    "user" int4 NOT NULL,
    organization int4 NOT NULL,
    owner bool NOT NULL DEFAULT false,
    hosting_admin bool NOT NULL DEFAULT false,
    group_admin bool NOT NULL DEFAULT false,
    group_manager bool NOT NULL DEFAULT false,
    CONSTRAINT users_organizations_pk PRIMARY KEY ("user", organization),
    CONSTRAINT users_organizations_organizations_fk FOREIGN KEY (organization) REFERENCES organizations(id) ON UPDATE RESTRICT ON DELETE CASCADE,
    CONSTRAINT users_organizations_users_fk FOREIGN KEY ("user") REFERENCES users(id) ON UPDATE RESTRICT ON DELETE CASCADE
)
WITH (
OIDS=FALSE
) ;
CREATE INDEX users_organizations_group_admin_idx ON users_organizations USING btree (group_admin) ;
CREATE INDEX users_organizations_hosting_admin_idx ON users_organizations USING btree (hosting_admin) ;

CREATE TYPE group_visibility AS ENUM ('public', 'hidden', 'private');

CREATE TABLE public.groups (
    id serial NOT NULL,
    organization int4 NOT NULL,
    short_name varchar NOT NULL,
    display_name varchar NOT NULL,
    include_admins bool NOT NULL DEFAULT true,
    include_managers bool NOT NULL DEFAULT true,
    visibility group_visibility NOT NULL DEFAULT 'public'::group_visibility,
    CONSTRAINT groups_pk PRIMARY KEY (id),
    CONSTRAINT groups_short_name_un UNIQUE (organization, short_name),
    CONSTRAINT groups_organizations_fk FOREIGN KEY (organization) REFERENCES organizations(id) ON UPDATE RESTRICT ON DELETE CASCADE
)
WITH (
OIDS=FALSE
) ;

CREATE TABLE public.users_groups (
    "user" int4 NOT NULL,
    "group" int4 NOT NULL,
    manager bool NOT NULL DEFAULT false,
    implicit bool NOT NULL DEFAULT false,
    CONSTRAINT users_groups_pk PRIMARY KEY ("user", "group"),
    CONSTRAINT users_groups_groups_fk FOREIGN KEY ("group") REFERENCES groups(id) ON UPDATE RESTRICT ON DELETE CASCADE,
    CONSTRAINT users_groups_users_fk FOREIGN KEY ("user") REFERENCES users(id) ON UPDATE RESTRICT ON DELETE CASCADE
)
WITH (
OIDS=FALSE
) ;
CREATE INDEX users_groups_manager_idx ON users_groups USING btree (manager) ;

CREATE TABLE public.server_keys (
    id serial NOT NULL,
    organization int4 NOT NULL,
    hostname varchar NOT NULL,
    "key" varchar NOT NULL,
    CONSTRAINT server_keys_key_un UNIQUE (key),
    CONSTRAINT server_keys_pk PRIMARY KEY (id),
    CONSTRAINT server_keys_organizations_fk FOREIGN KEY (organization) REFERENCES organizations(id) ON UPDATE RESTRICT ON DELETE CASCADE
)
WITH (
OIDS=FALSE
) ;
