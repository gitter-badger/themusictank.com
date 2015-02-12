module Entity
    # Represent a release by a musician or band.
    class Sluggable < Entity::Base

        # This does not have a table associated.
        # Models will inherit this class and overrite the fields.
        self.abstract_class = true

        # Slugs are required and unique
        validates :slug, presence: true, uniqueness: true

        # Generate the slug when creating a record
        before_validation(on: :create) do
            log "before_validation: Starting validation loop on slugs."

            slug = self.generate_slug
            if Repository::Sluggables.slug_exists?(self, slug)
                # Loop for unique slugs.
                counter = 1;
                while Repository::Sluggables.slug_exists?(self, "#{slug}-#{counter}")
                    counter += 1
                end
                self.slug = "#{slug}-#{counter}"
            else
                self.slug = slug
            end
        end

        # Generates a slug based on entity attributes
        def generate_slug
            slug = nil
            slug = self.name.parameterize unless !self.respond_to? 'name' or !slug.nil?
            slug = self.title.parameterize unless !self.respond_to? 'title' or !slug.nil?

            unless (slug.length > 0)
                unsupported_length_replacement
            else
                slug
            end
        end

        def unsupported_length_replacement
            "_"
        end

    end
end
