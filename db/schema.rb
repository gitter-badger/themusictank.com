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

ActiveRecord::Schema.define(version: 20150219194301) do

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
    t.integer  "is_popular",         limit: 1
    t.datetime "last_lastfm_update"
    t.datetime "last_mb_update"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.text     "bio"
  end

  add_index "artists", ["slug"], name: "index_artists_on_slug"

  create_table "authentications", force: true do |t|
    t.integer  "user_id"
    t.string   "provider"
    t.string   "uid"
    t.string   "token"
    t.datetime "created_at"
    t.datetime "updated_at"
  end

  create_table "similar_artists", id: false, force: true do |t|
    t.integer "artist_id",         null: false
    t.integer "similar_artist_id", null: false
  end

  add_index "similar_artists", ["artist_id", "similar_artist_id"], name: "index_similar_artists_on_artist_id_and_similar_artist_id", unique: true

  create_table "tracks", force: true do |t|
    t.string   "title"
    t.string   "slug"
    t.string   "mbid",                limit: 36, null: false
    t.integer  "position"
    t.integer  "duration"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "youtube_key"
    t.datetime "last_youtube_update"
  end

  create_table "users", force: true do |t|
    t.string   "provider"
    t.string   "uid"
    t.string   "name"
    t.string   "oauth_token"
    t.datetime "oauth_expires_at"
    t.datetime "created_at"
    t.datetime "updated_at"
    t.string   "email",                  default: "", null: false
    t.string   "encrypted_password",     default: "", null: false
    t.string   "reset_password_token"
    t.datetime "reset_password_sent_at"
    t.datetime "remember_created_at"
    t.integer  "sign_in_count",          default: 0,  null: false
    t.datetime "current_sign_in_at"
    t.datetime "last_sign_in_at"
    t.string   "current_sign_in_ip"
    t.string   "last_sign_in_ip"
  end

  add_index "users", ["email"], name: "index_users_on_email", unique: true
  add_index "users", ["reset_password_token"], name: "index_users_on_reset_password_token", unique: true

end
