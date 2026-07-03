# -*- coding: utf-8 -*-
from flask import Flask, render_template, request, redirect, url_for, session, jsonify, Response
from data.career_data import CAREERS
import database
import planner

app = Flask(__name__)
app.secret_key = "smart-planner-dream-career-secret-key"

database.init_db()


@app.route("/")
def landing():
    return render_template("landing.html")


@app.route("/input", methods=["GET"])
def input_form():
    return render_template("input_form.html", careers=CAREERS)


@app.route("/generate", methods=["POST"])
def generate():
    nama = request.form.get("nama", "").strip()
    program_studi = request.form.get("program_studi", "Ilmu Komputer")
    semester = int(request.form.get("semester", 1))
    career_key = request.form.get("career_key")
    minat = request.form.get("minat", "").strip()
    skills = request.form.getlist("skills")

    if not nama or career_key not in CAREERS:
        return redirect(url_for("input_form"))

    roadmap = planner.build_roadmap(career_key, semester, skills)

    mahasiswa_id = database.save_mahasiswa(
        nama, program_studi, semester, career_key, skills, minat, roadmap
    )
    session["mahasiswa_id"] = mahasiswa_id
    return redirect(url_for("roadmap_view", mahasiswa_id=mahasiswa_id))


@app.route("/roadmap/<int:mahasiswa_id>")
def roadmap_view(mahasiswa_id):
    mhs = database.get_mahasiswa(mahasiswa_id)
    if not mhs:
        return redirect(url_for("input_form"))
    session["mahasiswa_id"] = mahasiswa_id
    progress = database.get_progress_map(mahasiswa_id)
    return render_template("roadmap.html", mhs=mhs, progress=progress)


@app.route("/roadmap/<int:mahasiswa_id>/semester/<int:semester_no>")
def semester_detail(mahasiswa_id, semester_no):
    mhs = database.get_mahasiswa(mahasiswa_id)
    if not mhs:
        return redirect(url_for("input_form"))
    session["mahasiswa_id"] = mahasiswa_id
    progress = database.get_progress_map(mahasiswa_id)

    sem = next((s for s in mhs["roadmap"]["semesters"] if s["semester_no"] == semester_no), None)
    if sem is None:
        return redirect(url_for("roadmap_view", mahasiswa_id=mahasiswa_id))

    by_category = {"skill": [], "portfolio": [], "certification": [], "magang": []}
    for a in sem["actions"]:
        by_category[a["category"]].append(a)

    all_resources = []
    for a in sem["actions"]:
        all_resources.extend(a.get("resources", []))

    return render_template(
        "detail_semester.html",
        mhs=mhs, sem=sem, progress=progress,
        by_category=by_category, all_resources=all_resources,
    )


@app.route("/toggle/<int:mahasiswa_id>/<action_id>", methods=["POST"])
def toggle(mahasiswa_id, action_id):
    new_status = database.toggle_progress(mahasiswa_id, action_id)
    return jsonify({"done": new_status})


@app.route("/roadmap/<int:mahasiswa_id>/download")
def download_roadmap(mahasiswa_id):
    mhs = database.get_mahasiswa(mahasiswa_id)
    if not mhs:
        return redirect(url_for("input_form"))
    lines = []
    lines.append(f"ROADMAP KARIER - {mhs['nama']}")
    lines.append(f"Program Studi : {mhs['program_studi']}")
    lines.append(f"Semester Saat Ini : {mhs['semester']}")
    lines.append(f"Target Karier : {mhs['roadmap']['career_label']}")
    lines.append("=" * 60)
    for sem in mhs["roadmap"]["semesters"]:
        lines.append(f"\nSemester {sem['semester_no']} - {sem['phase_label']} ({sem['focus_tag']})")
        for a in sem["actions"]:
            lines.append(f"  - {a['name']}")
    lines.append("\n" + "=" * 60)
    lines.append("Dibuat oleh Smart Planner Pengembangan Karier Mahasiswa (Goal-Based AI)")
    text = "\n".join(lines)
    return Response(
        text, mimetype="text/plain",
        headers={"Content-Disposition": f"attachment;filename=roadmap_{mhs['nama'].replace(' ', '_')}.txt"},
    )


@app.route("/profil")
def profil():
    mahasiswa_id = session.get("mahasiswa_id")
    mhs = database.get_mahasiswa(mahasiswa_id) if mahasiswa_id else None
    return render_template("profil.html", mhs=mhs)


@app.route("/panduan")
def panduan():
    return render_template("panduan.html")


@app.route("/tentang")
def tentang():
    return render_template("tentang.html")


if __name__ == "__main__":
    app.run(debug=True, port=5000)
