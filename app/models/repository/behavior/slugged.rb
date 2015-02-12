module Repository
    module Behavior
        # This class handles queries of Artists objects.
        module Slugged
            include Repository::Base

            # TODO : This is horrible. Make it suck less.
            # I can't figure out why entity.class keeps on using the :mbid of entity.mbid while creating the exist? query.
            # Because of that, I have to run the count query by hand to ensure we only just compare the slug.
            def self.slug_exists? entity, slug
                entity.class.count_by_sql(["SELECT count(id) FROM #{entity.class.table_name} where slug = :slug", {slug: slug}]) > 0
            end

        end
    end
end
