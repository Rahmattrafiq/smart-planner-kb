# -*- coding: utf-8 -*-
from flask import Flask, render_template, request, redirect, url_for, session, jsonify, Response, flash
from data.career_data import CAREERS
import database
import planner

app = Flask(__name__)
app.secret_key = "smart-planner-dream-career-secret-key"

database.init_db()


@app.before_request
def require_login():
    public_endpoints = {"landing", "login", "register", "logout", "panduan", "tentang", "static"}
    if request.endpoint in public_endpoints:
        return None
    if not session.get("user_id"):
        flash("Silakan login terlebih dahulu untuk melanjutkan.", "error")
        return redirect(url_for("login"))
    return None


@app.route("/")
def landing():
    return render_template("landing.html")


@app.route("/login", methods=["GET", "POST"])
def login():
    if session.get("user_id"):
        return redirect(url_for("input_form"))

    if request.method == "POST":
        email = request.form.get("email", "").strip().lower()
        password = request.form.get("password", "")
        user = database.authenticate_user(email, password)
        if user:
            session["user_id"] = user["id"]
            session["user_name"] = user["nama"]
            flash("Login berhasil. Selamat melanjutkan perjalanan kariermu.", "success")
            return redirect(url_for("input_form"))
        flash("Email atau password salah. Silakan coba lagi.", "error")

    return render_template("login.html")


@app.route("/register", methods=["GET", "POST"])
def register():
    if session.get("user_id"):
        return redirect(url_for("input_form"))

    if request.method == "POST":
        nama = request.form.get("nama", "").strip()
        email = request.form.get("email", "").strip().lower()
        password = request.form.get("password", "")
        confirm_password = request.form.get("confirm_password", "")
        errors = []

        if len(nama) < 2:
            errors.append("Nama minimal terdiri dari 2 karakter.")
        if "@" not in email or "." not in email:
            errors.append("Format email tidak valid.")
        if len(password) < 6:
            errors.append("Password minimal 6 karakter.")
        if password != confirm_password:
            errors.append("Konfirmasi password tidak sesuai.")
        if database.get_user_by_email(email):
            errors.append("Email sudah terdaftar. Silakan gunakan email lain.")

        if errors:
            for message in errors:
                flash(message, "error")
            return render_template("register.html", form_data=request.form.to_dict())

        user_id = database.register_user(nama, email, password)
        session["user_id"] = user_id
        session["user_name"] = nama
        flash("Akun berhasil dibuat. Silakan lanjutkan membuat roadmap.", "success")
        return redirect(url_for("input_form"))

    return render_template("register.html", form_data={})


@app.route("/logout")
def logout():
    session.clear()
    flash("Kamu telah keluar dari akun.", "success")
    return redirect(url_for("landing"))


@app.route("/input", methods=["GET"])
def input_form():
    return render_template("input_form.html", careers=CAREERS, errors=[], form_data={})


@app.route("/generate", methods=["POST"])
def generate():
    nama = request.form.get("nama", "").strip()
    program_studi = request.form.get("program_studi", "").strip()
    semester_raw = request.form.get("semester", "")
    career_key = request.form.get("career_key", "")
    minat = request.form.get("minat", "").strip()
    skills = request.form.getlist("skills")

    errors = []
    if len(nama) < 2:
        errors.append("Nama harus diisi minimal 2 karakter.")
    if not program_studi:
        errors.append("Program studi harus dipilih.")
    try:
        semester = int(semester_raw)
    except (TypeError, ValueError):
        semester = 0
    if semester not in range(1, 9):
        errors.append("Semester harus dipilih dari 1 sampai 8.")
    if career_key not in CAREERS:
        errors.append("Target karier belum dipilih.")
    if not skills:
        errors.append("Pilih minimal satu skill yang sudah kamu kuasai.")

    if errors:
        for message in errors:
            flash(message, "error")
        return render_template(
            "input_form.html",
            careers=CAREERS,
            errors=errors,
            form_data=request.form.to_dict(),
        )

    roadmap = planner.build_roadmap(career_key, semester, skills)

    mahasiswa_id = database.save_mahasiswa(
        nama,
        program_studi,
        semester,
        career_key,
        skills,
        minat,
        roadmap,
        user_id=session.get("user_id"),
    )
    session["mahasiswa_id"] = mahasiswa_id
    flash("Roadmap berhasil dibuat. Yuk lanjutkan progresmu.", "success")
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
    user = database.get_user_by_id(session.get("user_id")) if session.get("user_id") else None
    return render_template("profil.html", mhs=mhs, user=user)


@app.route("/panduan")
def panduan():
    return render_template("panduan.html")


@app.route("/tentang")
def tentang():
    return render_template("tentang.html")


if __name__ == "__main__":
    app.run(debug=True, port=5000)
