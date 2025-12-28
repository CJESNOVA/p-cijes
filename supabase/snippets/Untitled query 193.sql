CREATE TABLE public.countries (
  id uuid PRIMARY KEY,
  name text NOT NULL,
  iso_code text,
  calling_code text,
  flag_url text,
  language_id uuid,
  currency_id uuid
);
