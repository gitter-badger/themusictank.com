# encoding: UTF-8
# This file is auto-generated from the current state of the database. Instead
# of editing this file, please use the migrations feature of Active Record to
# incrementally modify your database, and then regenerate this schema definition.
#
# Note that this schema.rb definition is the authoritative source for your
# database schema. If you need to create the application database on another
# system, you should be using db:schema:load, not running all the migrations
# from scratch. The latter is a flawed and unsustainable approach (the more migrations
# you'll amass, the slower it'll run and the greater likelihood for issues).
#
# It's strongly recommended that you check this file into your version control system.

ActiveRecord::Schema.define(version: 20150213173813) do

  create_table "albums", force: true do |t|
    t.string   "title"
    t.string   "slug"
    t.string   "mbid",               limit: 36, null: false
    t.string   "thumbnail_source"
    t.string   "thumbnail"
    t.datetime "release_date"
    t.integer  "artist_id"
    t.datetime "last_lastfm_update"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.integer  "playcount"
  end

  add_index "albums", ["artist_id"], name: "index_albums_on_artist_id"
  add_index "albums", ["slug"], name: "index_albums_on_slug"

  create_table "albums_tracks", force: true do |t|
    t.integer "track_id"
    t.integer "album_id"
  end

  add_index "albums_tracks", ["album_id", "track_id"], name: "index_albums_tracks_on_album_id_and_track_id"
  add_index "albums_tracks", ["track_id"], name: "index_albums_tracks_on_track_id"

  create_table "artists", force: true do |t|
    t.string   "name",                          null: false
    t.string   "slug"
    t.string   "mbid",               limit: 36, null: false
    t.string   "thumbnail_source"
    t.string   "thumbnail"
    t.boolean  "is_popular"
    t.datetime "last_lastfm_update"
    t.datetime "last_mb_update"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.text     "bio"
  end

  add_index "artists", ["slug"], name: "index_artists_on_slug"

  create_table "similar_artists", id: false, force: true do |t|
    t.integer "artist_id",         null: false
    t.integer "similar_artist_id", null: false
  end

  add_index "similar_artists", ["artist_id", "similar_artist_id"], name: "index_similar_artists_on_artist_id_and_similar_artist_id", unique: true

  create_table "tracks", force: true do |t|
    t.string   "title"
    t.string   "slug"
    t.string   "mbid",       limit: 36, null: false
    t.integer  "position"
    t.integer  "duration"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

end
